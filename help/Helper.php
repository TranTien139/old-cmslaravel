<?php
function CacheCollectionHome($type)
{
    $data_collection = [];
    $data_article = [];
    $data_user = [];
    $collections = \App\Models\Collection::join('meta_collection', 'collection.id', '=', 'meta_collection.collection_id')
        ->select('collection.*')
        ->where('status', 2)
        ->where('type', $type)
        ->orderBy('updated_at', 'desc')
        ->groupBy('collection_id')
        ->paginate(8);
    foreach ($collections as $items) {
        $data_article[] = $items->getArticle()->take(4)->orderBy('id', 'desc')->get();
        $data_user[] = $items->getUser;
        $data_collection[] = $items;
    }
    \Cache::forget('collection_' . $type);
    \Cache::forever('collection_' . $type, [$data_article, $data_collection, $data_user]);
}

function ParseTag($v, $article = null)
{
    if ($article != null) {
        if (isset($article->tags) && json_decode($article->tags)) {
            foreach (json_decode($article->tags) as $item) {
                foreach ($item as $k => $tag) {
                    @DeleteZKeyIndex('category_article_' . $k, $article->id);
                }
            }
        }
        if ($article->status != 'publish') {
            @RemoveIndexArticle($article);
        }
        @DeleteKeyRedis('article_category_' . $article->id);
    }
    $meta_final = [];
    if ($v != null) {
        $tags = explode('||', $v);
        $meta_final = [];
        foreach ($tags as $items) {
            if (\App\Models\Category::where('title', $items)->where('type', 'tag')->count() > 0) {
                $tag = \App\Models\Category::where('title', $items)->where('type', 'tag')->first();
                if ($article != null && $article->status == 'publish') {
                    CategoryArticle($article->id, $tag->id, strtotime($article->published_at));
                }
                $meta_final[] = [$tag->id => $tag->title];
            } else {
                $tag = new \App\Models\Category();
                $tag->title = $items;
                $tag->slug = str_slug($items);
                $tag->type = 'tag';
                $tag->parent_id = null;
                $tag->status = 1;
                $tag->save();

                if ($article != null && $article->status == 'publish') {
                    CategoryArticle($article->id, $tag->id, strtotime($article->published_at));
                }
                $meta_final[] = [$tag->id => $items];
            }
        }
    }
    if (!empty($meta_final)) {
        return json_encode($meta_final);
    } else {
        return null;
    }
}


function AddArtcileToTags($article = null)
{
    if ($article != null) {
        if (isset($article->tags) && json_decode($article->tags)) {
            foreach (json_decode($article->tags) as $item) {
                foreach ($item as $k => $tag) {
                    @DeleteZKeyIndex('category_article_' . $k, $article->id);
                    @CategoryArticle($article->id, $k , strtotime($article->published_at));
                }
            }
        }
    }
}

function ParseRelate($v)
{
    $meta_final = [];
    $v = str_replace('\,', ':abc', $v);
    $relates = explode('||', $v);
    if ($v != null) {
        foreach ($relates as $items) {
            $items = str_replace(':abc', ',', $items);
            $check = \App\Models\Article::where('title', $items)->count();
            if ($check > 0) {
                $relate = \App\Models\Article::where('title', $items)->first();
                $meta_final[] = [$relate->id => $relate->title];
            }
        }
    }
    if (!empty($meta_final)) {
        return json_encode($meta_final);
    } else {
        return null;
    }

}


function ParseCategory($v, $article)
{
    if (isset($article->articleCategory)) {
        $categories_1 = $article->articleCategory;
        foreach ($categories_1 as $cat) {
            @DeleteZKeyIndex('category_article_' . $cat->id, $article->id);
        }
    }
    if ($article->status != 'publish') {
        @RemoveIndexArticle($article);
    }
    $article->articleCategory()->detach();
    @DeleteKeyRedis('article_category_' . $article->id);
    $categories = $v;
    if (!empty($categories)) {
        if (!in_array(79, $categories)) {
            $categories[] = 79;
        }
        foreach ($categories as $cate) {
            if ($article->status == 'publish') {
                CategoryArticle($article->id, $cate, strtotime($article->published_at));
            }
            $data_category[] = $cate;
            $meta_insert = new \App\Models\ArticleCategory();
            $meta_insert->category_id = $cate;
            $meta_insert->article_id = $article->id;
            $meta_insert->save();
        }
    }
}

function MetaDataProgress($meta_data, $article)
{
    if (!empty($meta_data)) {
        foreach ($meta_data as $k => $v) {
            if ($k == 'related' || $k == 'tags') {
                if ($k == 'tags') {
                    if ($v != null) {
                        $tags = explode('||', $v);
                        $meta_final = [];
                        foreach ($tags as $items) {
                            if (\App\Models\Category::where('title', $items)->where('type', 'tag')->count() > 0) {
                                $tag = \App\Models\Category::where('title', $items)->where('type', 'tag')->first();
                                $meta_final[] = [$tag->id => $tag->title];
                            } else {
                                $tag = new \App\Models\Category();
                                $tag->title = $items;
                                $tag->slug = str_slug($items);
                                $tag->type = 'tag';
                                $tag->parent_id = null;
                                $tag->status = 1;
                                $tag->save();

                                $meta_final[] = [$tag->id => $items];
                            }
                        }
                    }

                } else {
                    $meta_final = [];
                    $v = str_replace('\,', ':abc', $v);
                    $relates = explode('||', $v);
                    if ($v != null) {
                        foreach ($relates as $items) {
                            $items = str_replace(':abc', ',', $items);
                            $check = \App\Models\Article::where('title', $items)->count();
                            if ($check > 0) {
                                $relate = \App\Models\Article::where('title', $items)->first();
                                $meta_final[] = [$relate->id => $relate->title];
                            }
                        }
                    }

                }
                return json_encode($meta_final);
            } elseif ($k == 'category') {

            }
        }
    }
    return $data_category;
}

function TimeStatusPublish($article)
{
    if (\Input::get('status') == 'schedule') {
        $datetime_publish = date('Y-m-d H:i', strtotime(\Input::get('publish_date') . ' ' . \Input::get('publish_time')));
        $article->published_at = $datetime_publish;
    } elseif (\Input::get('status') == 'publish') {
        $datetime_publish = date('Y-m-d H:i', strtotime(\Input::get('publish_date') . ' ' . \Input::get('publish_time')));
        $article->published_at = $datetime_publish;
    } else {
        $article->published_at = null;
    }
    return $article;
}

function ScanGallery()
{
    $gallery_path = '';
    if (\Input::get('gallery') != '') {
        $folder_gallery = \Input::get('gallery');
        if (is_dir($folder_gallery)) {
            foreach (scandir($folder_gallery) as $k => $items) {
                if ($k >= 3) {
                    $data[] = str_replace('//', '/', $folder_gallery . '/' . $items);
                }
            }
            $gallery_path = json_encode($data);
        } else {
            $gallery_path = '';
        }
    } else {
        $gallery_path = '';
    }
    return $gallery_path;
}

function CropImage($thumbnail)
{
    if (!empty($thumbnail)) {
        $this->resizeImage($thumbnail, 300, 180);
        $this->resizeImage($thumbnail, 96, 72);
    }
}

function ImportCacheObject($object, $type)
{
    $article = $object;
    $method = !empty(get_class_methods($article)) ? get_class_methods($article) : null;
    foreach ($method as $k => $v) {
        if ($v == '__construct' && $k < 1) break;
        $article->$v = $article->$v;
    }

    \Cache::forever($type . '_' . $object->id, $article);

}

function CreateFile($data)
{
    $var = '<!DOCTYPE html><html>
        <head>
        <title>Video Feedy</title>
        <meta property="og:type" content="article" />
        <meta property="og:site_name" content="Blogtamsu />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <!-- VideoJs required styles-->
            <link rel="stylesheet" type="text/css"
            href="http://m.feedy.vn/dist/plugins/videojs/videojs/videojs_5/video-js.css?v=415"/>
            <!-- VideoJs.vast.vpaid required styles-->
            <link rel="stylesheet" type="text/css"
            href="http://m.feedy.vn/dist/plugins/videojs/styles/videojs.vast.vpaid.css?v=415"/>
         <meta name="revisit-after" content="1 days">
         <meta http-equiv="content-language" content="vi" />
         <meta name="robots" content="noindex" />
         </head>
         <body>
        <form id="vast-vpaid-form">
            <div class="row">
                    <div style="float: left;padding: 10px 0;font-size: 16px;font-weight: 700;text-align: initial;line-height: 20px;width:100%">' . $data['title'] . '</div>

                    <div class="vjs-video-container" id="vjs-video-container-151286" style="width:' . $data['width'] . 'px;height:' . $data['height'] . 'px" data-image="' . $data['avatar'] . '" data-link="' . $data['link_mp4'] . '" data-id="151286">
                  </div>
            </div>
        </form>
        <script type="text/javascript" src="http://m.blogtamsu.vn/wp-content/themes/news/js/jquery-1.7.1.min.js"></script>
            <!-- VideoJs required scripts-->
            <script type="text/javascript" src="http://m.feedy.vn/dist/plugins/videojs/videojs/videojs_5/video.js?v=415"></script>
            <script type="text/javascript" src="http://m.feedy.vn/dist/plugins/videojs/scripts/es5-shim.js?v=415"></script>
            <script type="text/javascript" src="http://m.feedy.vn/dist/plugins/videojs/scripts/ie8fix.js?v=415"></script>
            <script type="text/javascript" src="http://m.feedy.vn/dist/plugins/videojs/scripts/videojs_5.vast.vpaid.js?v=415"></script>
            <script type="text/javascript" src="http://m.feedy.vn/dist/plugins/videojs/videojs/scripts/videojs.js?v=53"></script>
         </body>
         </html>';
    $dir = __DIR__ . '/../' . '/public/default_player_IA.html';
    $fp = fopen($dir, "w+");

    if (!$fp) {

    } else {
        fwrite($fp, $var, strlen($var));
        fclose($fp);
    }
}

if (!function_exists('time_elapsed_string')) {
    function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'năm',
            'm' => 'tháng',
            'w' => 'tuần',
            'd' => 'ngày',
            'h' => 'giờ',
            'i' => 'phút',
            's' => 'giây',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' trước' : 'mới';
    }
}


//REDIS ARTICLE AND CATEGORY

// LƯU CATEGORY VÀO REDIS
function CategoryRedis($id = 84, $action = 'edit')
{
    $id_key = 'categories_' . $id;
    $redis = new Redis();
    $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
    if ($action == 'edit') {
        $data = \App\Models\Category::find($id);
        $childs = \App\Models\Category::where('parent_id', $id)->get();
        foreach ($childs as $child) {
            $ids_key = 'categories_' . $child->id;
            if (isset($child->id) && \Cache::has($ids_key))
                $childs_array [] = $child->id;
        }
        $childs_json = json_encode([]);
        if (!empty($childs_array)) {
            $childs_json = json_encode($childs_array);
        }
        $data->childs_json = $childs_json;
        $data_json = (string)$data;
        $range = $redis->lRange('categories', 0, -1);
        if (\Cache::has($id_key) && count($range) > 0) {
            $key = \Cache::get($id_key);
            $redis->lSet('categories', (int)$key - 1, $data_json);
        } else {
            $key = $redis->rPush('categories', $data_json);
            $key = ($key);
            \Cache::forever($id_key, $key);
        }
    } elseif ($action == 'delete') {
        if (\Cache::has($id_key)) {
            $key = \Cache::get($id_key);
            // $redis->blPop('categories', (int)$key - 1);
            \Cache::forget($id_key);
        }
    }
}

// LƯU QUAN HỆ NHIỀU NHIỀU CATEGORY ARTICLE
function CategoryArticle($article_id = 113, $category = null, $score = 0)
{
    if ($category != null) {
        $redis = new Redis();
        $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
        $redis->zAdd('article_category_' . $article_id, $article_id, $category);
        $redis->zAdd('category_article_' . $category, $score, $article_id);
    };

}

//Xóa Index Article trong Key Category_Article
function RemoveIndexArticle($article)
{
    if (isset($article->articleCategory)) {
        $categories_1 = $article->articleCategory;
        foreach ($categories_1 as $cat) {
            @DeleteZKeyIndex('category_article_' . $cat->id, $article->id);
        }
    }
}

function AddIndexArticle($article)
{
    if (isset($article->articleCategory)) {
        $categories_1 = $article->articleCategory;
        foreach ($categories_1 as $cat) {
            $redis = new Redis();
            $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
            $redis->zAdd('category_article_' . $cat->id, strtotime($article->published_at), $article->id);
        }
    }
}

//XÓA KEY REDIS
function DeleteKeyRedis($key)
{
    $redis = new Redis();
    $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
    $redis->delete($key);
}

//XÓA ZKEY INDEX REDIS
function DeleteZKeyIndex($key, $index)
{
    $redis = new Redis();
    $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
    $redis->zDelete($key, (string)$index);
}


