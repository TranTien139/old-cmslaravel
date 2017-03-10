<?php
/**
 * Created by PhpStorm.
 * User: tanlinh
 * Date: 2/27/2016
 * Time: 10:06 AM
 */
if (!function_exists('get_gravatar')) {
    function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array())
    {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            $url = '<img src="' . $url . '"';
            foreach ($atts as $key => $val)
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }
}


function cate_parent($data, $parent = null, $str = "", $select = 0)
{
    foreach ($data as $value) {
        $id = $value["id"];
        $name = $value["title"];
        if ($value["parent_id"] == $parent) {
            if ($select != 0 && $id == $select) {
                echo "<option value ='$id' selected='selected'>$str $name</option>";
            } else {
                echo " <option value = '$id'>$str $name</option>";
            }
            cate_parent($data, $id, $str . "--", $select);
        }
    }
}

function cate_parent_checkbox($data, $parent = 0, $str = "", $select = 0)
{
    foreach ($data as $value) {
        $id = $value["id"];
        $name = $value["title"];
        if ($value["parent_id"] == $parent) {
            $html = '<div class="form-group category-item">' . $str . '
                    <label>
                        <input type="checkbox" class="minimal" name="category[]"
                               value="' . $id . '">' . $name . '
                    </label>
                    <i class="fa fa-flag flag_click" data-id="' . $id . '"></i>
                </div>';
            echo $html;
            cate_parent_checkbox($data, $id, $str . "++");
        }
    }
}

function cate_parent_selected($data, $parent = 0, $str = "", $id_article, $select = 0, $parent_category = null)
{

    foreach ($data as $value) {
        $active = '';
        $id = $value["id"];
        $name = $value["title"];
        $selected = DB::table('article_category')
            ->select('id')
            ->where('article_id', $id_article)
            ->where('category_id', $id)
            ->count();


        if ($value["parent_id"] == $parent) {
            if ($id == $parent_category) $active = 'style="color: red;"';
            if ($selected > 0) {
                $html = '<div class="form-group category-item">' . $str . '
                    <label>
                        <input type="checkbox" checked="checked" class="minimal" name="category[]"
                               value="' . $id . '">' . $name . '
                    </label>
                    <i class="fa fa-flag flag_click"  ' . $active . ' data-id="' . $id . '"></i>
                </div>';
            } else {
                $html = '<div class="form-group category-item">' . $str . '
                    <label>
                        <input type="checkbox" class="minimal" name="category[]"
                               value="' . $id . '">' . $name . '
                    </label>
                    <i class="fa fa-flag flag_click "   ' . $active . '  data-id="' . $id . '"></i>
                </div>';
            }
            echo $html;
            cate_parent_selected($data, $id, $str . "==", $id_article, $selected, $parent_category);
        }
    }
}


function FindChildrenCategory($id)
{
    $array_cate = '';
    $data = DB::table('category')->where('type', 'Category')->select('id', 'parent_id')->where('parent_id', $id)->get();
    if (count($data) > 0) {
        foreach ($data as $value) {
            $array_cate .= $value->id . ',';
            if ($value->parent_id != null && $value->parent_id != $id) {
                FindChildrenCategory($value->id);
            }
        }
    }
    return $array_cate;
}

function FindCategory($id)
{
    $array_cate = '';
    $data = DB::table('category')->where('type', 'Category')->select('id', 'parent_id')->where('id', $id)->get();
    if (count($data) > 0) {
        foreach ($data as $value) {
            $array_cate .= $value->id . ',';
        }
    }
    return $array_cate;
}

function curPageURL()
{
    $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (strpos($url, '?') == false) {
        $url .= '?';
    }
    if (preg_match('/&page=/', $url)) {
        $url = explode('&page=', $url);
        $url = $url[0];
    }
    echo $url;
}


function GenFileHome($id_cate, $filename, $style = 0)
{
    try {
        $filename = $filename . '.html';
        $category = \App\Models\Category::find($id_cate);
        $articles = getByCategory($category);
        $html = '';

        if (isset($articles)) {
            foreach ($articles as $article) {
                $creator = isset($article->getUser->name) ? $article->getUser->name : 'Anonymous';
                $str_cache = 'article_view_' . $article->id;
                $view = \Cache::has($str_cache) ? \Cache::get($str_cache) : rand(1000, 2000);
                $url = genLink($article, 'chi-tiet', $article->title, $article->id);
                // 0 la giao dien clip hot  1 la giao dien hai viet nam, hai the gioi
                if ($style == 0) {
                    $html .= '<li >';
                    $html .= '<a href = "' . $url . '" class="imgVideo w100pt left" >';
                    $html .= '<button href = "' . $url . '" class="btPlay" ><i class="fa fa-play" aria - hidden = "true" ></i ></button >';
                    $html .= '<div class="time" > 05:02 </div >';
                    $html .= '<button class="save" > Lưu</button >';
                    $html .= '<div class="bgR" ></div >';
                    $html .= '<img class="image_article" src = "' . get_thumbnail($article->thumbnail) . '" >';
                    $html .= '</a >';
                    $html .= '<a href = "' . $url . '" class="title" ><h2 >' . $article->title . '</h2 ></a >';
                    $html .= '<p > Đăng bởi: <b >' .$creator. '</b ></p >';
                    $html .= '<p ><b >' . number_format($view) . '</b > views ,<b >' . time_elapsed_string($article->published_at) . '</b ></p >';
                    $html .= '</li >';

                } else if ($style == 1) {
                    $html .= '<div class="slide" >';
                    $html .= '<a href = "' . $url . '" class="imgVideo w100pt left" >';
                    $html .= '<button href = "' . $url . '" class="btPlay" ><i class="fa fa-play" aria - hidden = "true" ></i ></button >';
                    $html .= '<div class="time" > 05:02 </div >';
                    $html .= '<button class="save" > Lưu</button >';
                    $html .= '<div class="bgR" ></div >';
                    $html .= '<img class="image_article" src = "' . get_thumbnail($article->thumbnail) . '" >';
                    $html .= '</a >';
                    $html .= '<a href = "' . $url . '" class="title" ><h2 >' . $article->title . '</h2 ></a >';
                    $html .= '<p > Đăng bởi: <b >' .$creator. '</b ></p >';
                    $html .= '<p ><b >' . number_format($view) . '</b > views ,<b >' . time_elapsed_string($article->published_at) . '</b ></p >';
                    $html .= '</div >';
                    $html .= 'hello';
                }
            }
        }

        Storage::disk('genfilehome')->put($filename, $html);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function getByCategory($category, $paginate = 8)
{
    $articles = $category->categoryArticle()->where(function ($articles) {
        $articles->where('status', 'publish');
    })->orderBy('published_at', 'desc')->orderBy('id', 'desc');

    $articles = $articles->paginate($paginate);
    return $articles;
}

function get_thumbnail($image, $width = null, $height = null)
{
    if ($image != '') {
        if (substr($image, 0, 4) == 'http') {
            return $image;
        }
        $image = str_replace(env('REPLACE_PATH'), '', $image);
        if ($width != null && $height != null) {
            $arr = explode('.', $image);
            $ext = end($arr);
            $imgs = explode('/', $arr[0]);
            $image_name = end($imgs);
            array_pop($imgs); //remove image name

            $dir = env('MEDIA_PATH') . implode('/', $imgs) . '/thumbnails';
            $new_image = $dir . '/' . $image_name . '_' . $width . 'x' . $height . '.' . $ext;
        } else {
            $new_image = env('MEDIA_PATH') . $image;
        }
    } else {
        $new_image = '/dist/imgDemo/a2.jpg';
    }

    return $new_image;
}

function genLink($blog, $route_name, $title, $id, $old_link = null)
{
    try {
        if ($blog->parent_category != null && $blog->parent_category != 0) {
            $category = \App\Models\Category::find($blog->parent_category);
            //$route_name = 'detail-article';
            if (!empty($category) && isset($category->title)) {
                $cat_slug = '/' . str_slug($category->title) . '/';
                $parent_id = $category->parent_id;
                while (true) {
                    if ($parent_id != 0) {
                        $child = \App\Models\Category::find($parent_id);
                        $cat_slug = '/' . str_slug($child->title) . '/' . $cat_slug;
                        $parent_id = $child->parent_id;
                    } else {
                        break;
                    }
                }
                $cat_slug = str_replace('//', '/', $cat_slug);
                $blog_link = '/video'. $cat_slug . str_slug($title) . '_' . $id . '.html';
            } else {
                $blog_link = $old_link;
            }

        } else {
            $blog_link = route($route_name) . '/' . str_slug($title) . '_' . $id . '.html';
        }
        return $blog_link;
    } catch (\Exception $e) {
        return $old_link;
    }
}

