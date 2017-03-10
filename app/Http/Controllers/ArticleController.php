<?php

namespace App\Http\Controllers;

use App\Cache\Article\ArticleInterface;
use App\Models\Ingredients;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\Event;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TraitController\ArticleDetail;
use App\Http\Controllers\TraitController\Tag;
use App\Repositories\Post\PostRepository;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\Models\ArticleRelate;
use App\Models\MetaArticle;
use Illuminate\Support\Facades\Storage;
use App\Models\Video;
use Auth;

class ArticleController extends Controller
{

    use ArticleDetail,
        Tag;

    public $article_on_page = 15;

    public function __construct(Request $request, PostRepository $article_interface, ArticleInterface $articleInterface)
    {
        $this->_request = $request;
        $this->_post = $article_interface;
        $this->_cache = $articleInterface;
    }

    public function getIndex(Request $request)
    {
//        $redis = new \Redis();
//        $redis->connect(env('REDIS_HOST'));
//        $data = $redis->zRange('article_category_' . 121, 0, -1);
//        $data = $redis->zRevRange('category_article_' . 73, 0, -1, true);
//        dd($data);
        $this->authorize('ViewArticle');
        $user_role = auth()->user()->user_type;
        try {
            $request->flash();
            $key = trim($request->get('key'));
            $category = trim($request->get('category'));
            $start_date = trim($request->get('start_date'));
            $end_date = trim($request->get('end_date'));
            $status = trim($request->get('status'));

            $cate_id = $category;

            if ($category != null) {
                $child = FindChildrenCategory($category);
                $cat = FindCategory($category);
                $lst1 = explode(',', $child);
                $lst2 = explode(',', $cat);
                $lst = array_merge($lst1, $lst2);
                $lst = array_unique($lst);
                $articles = Article::join('article_category', 'article_category.article_id', '=', 'article.id');
                if ($child != '') {
                    $articles = $articles->where(function ($query) use ($lst) {
                        $query->whereIn('article_category.category_id', $lst);
                    });
                } else {
                    $articles = $articles->where('article_category.category_id', $category);
                }

            } else {
                $articles = Article::select('id', 'title', 'slug', 'link_video', 'thumbnail', 'description', 'creator', 'created_at', 'updated_at', 'status', 'approve_by', 'published_at');
            }

            if ($key != null) {
                $articles = $articles->where('title', 'LIKE', '%' . $key . '%');
            }

            if ($start_date != null && $end_date != null) {
                $start_date = date('Y-m-d H:i:s', $start_date);
                $end_date = date('Y-m-d H:i:s', $end_date);
                $articles = $articles->whereRaw("(article.created_at BETWEEN '" . $start_date . "' AND  '" . $end_date . "')");
            }

            if ($status != null) {
                $articles = $articles->where('status', $status);
            }

            $articles = $articles->orderBy('article.created_at', 'desc')->paginate(10);
            $category = Category::where('type', 'Category')->where('status', 1)->get();

            if (!empty($articles)) {
                return view('childs.article.index')->with('articles', $articles)->with('category', $category)->with('role', $user_role)->with('cate_id', $cate_id)->with('status', $status)->with('start_date', $start_date)->with('end_date', $end_date);
            } else {
                echo 'no data';
            }
        } catch (Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    public function getCreate()
    {
        try {
            $this->authorize('CreateArticle');
            $category = Category::where('status', 1)->where('id', '!=', 79)->where('type', 'Category')->get();
            return view('childs.article.create', compact('category'));
        } catch (\Exception $e) {
            abort(404);
        }

    }


    function postCreate(Request $request)
    {
        $this->authorize('CreateArticle');
        try {
            //Find Article
            $article = new Article;
            //Init Variable
            $publish_total = $article->published_at;
            $data_article = [];
            $meta_data = [];
            $data_article['type'] = request()->has('type') ? request()->get('type') : null;
            $data_article['creator'] = Auth::user()->email;
            $data_article['parent_category'] = request()->has('parent_category') ? request()->get('parent_category') : null;
            $data_article['status'] = request()->has('status') ? request()->get('status') : null;
            $data_article['title'] = request()->has('title') ? request()->get('title') : null;
            $data_article['slug'] = request()->has('title') ? str_slug(request()->get('title')) : null;
            $data_article['video_id'] = request()->get('video_id') != '' ? request()->get('video_id') : null;
            $data_article['thumbnail'] = request()->has('thumbnail') ? request()->get('thumbnail') : null;
            $data_article['video_id'] = request()->has('video_id') ? request()->get('video_id') : null;
            $data_article['seo_title'] = request()->has('seo_title') ? request()->get('seo_title') : null;
            $data_article['seo_meta'] = request()->has('seo_meta') ? request()->get('seo_meta') : null;
            $data_article['seo_description'] = request()->has('seo_description') ? request()->get('seo_description') : null;
            $data_article['description'] = request()->has('description') ? request()->get('description') : null;
            if ($data_article['status'] == 'schedule') {
                $publish_date = request()->has('publish_date') ? request()->get('publish_date') : null;
                $publish_time = request()->has('publish_time') ? request()->get('publish_time') : null;
                $publish_total = trim($publish_date) . ' ' . trim($publish_time);
                $publish_total = date('Y-m-d H:i', strtotime($publish_total));
            }
            $data_article['published_at'] = $publish_total;

            $relateds = request()->has('related') ? request()->get('related') : null;
            $tags = request()->has('tags') ? request()->get('tags') : null;
            $meta_data['category'] = request()->has('category') ? request()->get('category') : null;

            $data_article['related'] = ParseRelate($relateds);
            $data_article['tags'] = ParseTag($tags);

            $cat = [];

            if (request()->get('category') != '') {
                $cat = explode(',', request()->get('category'));
                if ($data_article['parent_category'] != null) {
                    $parent = $data_article['parent_category'];
                    $category = Category::find($parent);
                    $cat[] = $category->id;
                    $parent_id = $category->parent_id;
                    while (true) {
                        if ($parent_id != 0) {
                            $child = Category::find($parent_id);
                            $cat[] = $child->id;
                            $parent_id = $child->parent_id;
                        } else {
                            break;
                        }
                    }
                    $cat = array_unique($cat);
                }
            }

            foreach ($data_article as $k => $v) {
                $article->$k = $v;
            }
            $article->save();
            ParseTag($tags, $article);


            if (!empty($data_article['thumbnail']) && (str_contains($data_article['thumbnail'], 'http://') == false)) {
                $this->resizeImage($data_article['thumbnail'], 300, 180);
                $this->resizeImage($data_article['thumbnail'], 96, 72);
                $this->resizeImage($data_article['thumbnail'], 600, 360);
                $this->resizeImage($data_article['thumbnail'], 230, 130);
            }

            if (!empty($cat)) {
                ParseCategory($cat, $article);
            }

            $this->_cache->getById($article->id);
            return json_encode(['status' => 'success', 'msg' => 'Lưu bài viết thành công']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function getEdit($article_id)
    {
        $this->authorize('EditArticle');
        try {
            $category = Category::where('type', 'Category')->where('id', '!=', 79)->where('status', 1)->get();
            $article = Article::findOrFail($article_id);
            $video_id = Video::select('id', 'title')->where('id', $article->video_id)->first();
            return view('childs.article.edit', compact('category', 'article', 'video_id'));
        } catch (Exception $ex) {
            return abort(404);
        }
    }

    public function postEdit($id)
    {
        $this->authorize('EditArticle');
        try {
            //Find Article
            $article = Article::findOrfail($id);

            //Init Variable
            $publish_total = $article->published_at;
            $data_article = [];
            $meta_data = [];
            $data_article['type'] = request()->has('type') ? request()->get('type') : null;
            $data_article['parent_category'] = request()->has('parent_category') ? request()->get('parent_category') : null;
            $data_article['status'] = request()->has('status') ? request()->get('status') : null;
            $data_article['title'] = request()->has('title') ? request()->get('title') : null;
            $data_article['slug'] = request()->has('title') ? str_slug(request()->get('title')) : null;
            $data_article['media_path'] = request()->has('media_path') ? request()->get('media_path') : null;
            $data_article['thumbnail'] = request()->has('thumbnail') ? request()->get('thumbnail') : null;
            $data_article['video_id'] = request()->has('video_id') ? request()->get('video_id') : null;
            $data_article['seo_title'] = request()->has('seo_title') ? request()->get('seo_title') : null;
            $data_article['seo_meta'] = request()->has('seo_meta') ? request()->get('seo_meta') : null;
            $data_article['seo_description'] = request()->has('seo_description') ? request()->get('seo_description') : null;
            $data_article['description'] = request()->has('description') ? request()->get('description') : null;

            if ($data_article['status'] == 'schedule') {
                $publish_date = request()->has('publish_date') ? request()->get('publish_date') : null;
                $publish_time = request()->has('publish_time') ? request()->get('publish_time') : null;
                $publish_total = trim($publish_date) . ' ' . trim($publish_time);
                $publish_total = date('Y-m-d H:i', strtotime($publish_total));
            }
            $data_article['published_at'] = $publish_total;


            $relateds = request()->has('related') ? request()->get('related') : null;
            $tags = request()->has('tags') ? request()->get('tags') : null;
            $meta_data['category'] = request()->has('category') ? request()->get('category') : null;

            $data_article['related'] = ParseRelate($relateds);
            $data_article['tags'] = ParseTag($tags, $article);
            $cat = [];

            if (request()->get('category') != '') {
                $cat = explode(',', request()->get('category'));
                if ($data_article['parent_category'] != null) {
                    $parent = $data_article['parent_category'];
                    $category = Category::find($parent);
                    $cat[] = $category->id;
                    $parent_id = $category->parent_id;
                    while (true) {
                        if ($parent_id != 0) {
                            $child = Category::find($parent_id);
                            $cat[] = $child->id;
                            $parent_id = $child->parent_id;
                        } else {
                            break;
                        }
                    }
                    $cat = array_unique($cat);
                }
            }

            if (!empty($cat)) {
                ParseCategory($cat, $article);
            }
            foreach ($data_article as $k => $v) {
                $article->$k = $v;
            }
            $article->save();
            if (!empty($data_article['thumbnail']) && (str_contains($data_article['thumbnail'], 'http://') == false)) {
                $this->resizeImage($data_article['thumbnail'], 300, 180);
                $this->resizeImage($data_article['thumbnail'], 96, 72);
                $this->resizeImage($data_article['thumbnail'], 600, 360);
                $this->resizeImage($data_article['thumbnail'], 230, 130);
            }
            $this->_cache->getById($article->id);
            return json_encode(['status' => 'success', 'msg' => 'Lưu bài viết thành công']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }
}
