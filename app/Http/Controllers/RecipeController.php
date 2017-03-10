<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MetaArticle;
use App\Models\Article;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Models\Ingredients;
use App\Models\IngredientRelation;
use App\Repositories\Post\PostRepository;
use App\Models\Step;
use App\Models\Event;
use Illuminate\Http\Request;

use App\Models\ArticleCategory;

class RecipeController extends Controller {

    public function __construct(PostRepository $article_interface) {
        $this->_post = $article_interface;
    }

    public function getCreate() {
        $this->authorize('CreateArticle');
        $category = Category::where('status',1)->where('type','Category')->get();
        return view('childs.recipe.create', compact('category'));
    }

    function postCreate(Request $request) {
        $this->authorize('CreateArticle');
        try {
            $meta_data = \Input::only(['title', 'youtube', 'description', 'publish_date','publish_time','status','seo_title', 'seo_meta', 'seo_description', 'type_article', 'tags', 'related','thumbnail']);
            $article = new Article;
            $article->title = $request->get('title');
            $article->slug = str_slug($request->get('title'));
            $article->link_video = $request->get('youtube');
            $article->description = $request->get('description');

            $thumbnail = !empty($request->get('thumbnail')) ? $request->get('thumbnail') : '';
            if (!empty($thumbnail) && (str_contains($thumbnail, 'http://') == false)) {
                $this->resizeImage($thumbnail, 500, 400);
                $article->thumbnail = $thumbnail;
            }else{
                $article->thumbnail = $thumbnail;
            }

            if (\Input::get('status') == '') {
                $article->status = 'schedule';
            }else{
                $article->status = $request->get('status');
            }

            if (\Input::get('status') == 'publish') {
                $article->published_at = date('Y-m-d H:i:s');
            }else{
                $datetime_publish = date('Y-m-d H:i', strtotime(\Input::get('publish_date') . ' ' . \Input::get('publish_time')));
                $article->published_at = $datetime_publish;
            } 

            $article->creator = \Auth::user()->email;
            $article->save();

            if($request->get('category') != ''){
            $cate_list = explode(',',$request->get('category')); }
            if(count($cate_list)>0){
            for($i=0; $i<count($cate_list); $i++){
                    $cate = new ArticleCategory;
                    $cate->category_id = $cate_list[$i];
                    $cate->article_id = $article->id;
                    $cate->save();
            }
            }

            if($request->get('list_tag') != ''){
            $list_tag= explode('||',$request->get('list_tag'));
            $meta_tag =[];
            for($i=0; $i<count($list_tag); $i++){
                    $slug = str_slug($list_tag[$i]);
                    $cate = Category::where('type','tags')->select('id','title')->where('slug',$slug)->first();
                    if(!isset($cate)){
                        $cat = new Category;
                        $cat->title = $list_tag[$i];
                        $cat->slug = $slug;
                        $cat->desc = $list_tag[$i];
                        $cat->type = 'tag';
                        $cat->status = 1;   
                        $cat->save();
                        $meta_tag[] = [$cat->id => $cat->title];
                    }else{
                        $meta_tag[] = [$cate->id => $cate->title];
                    }
               
             }
                $meta_tags = new MetaArticle ();
                $meta_tags->meta_key = 'tag';
                $meta_tags->meta_value = json_encode($meta_tag);
                $meta_tags->article_id = $article->id;
                $meta_tags->save();
            }

            if($request->get('list_related') != ''){
             $list_related= explode('||',$request->get('list_related'));
             $meta_related = [];
             $check = 0;
            for($i=0; $i<count($list_related); $i++){
                $slug = str_slug($list_related[$i]);
                $related_id = Article::select('id','title')->where('slug',$slug)->first();
                if(isset($related_id)){
                    $meta_related[] = [$related_id->id => $related_id->title]; $check =1; 
                }
                 }
               if($check ==1){
                $related_telate = new MetaArticle;
                $related_telate->meta_key = 'related';
                $related_telate->meta_value = json_encode($meta_related);
                $related_telate->article_id = $article->id;
                $related_telate->save();  }
            }

            //insert meta key
            $meta_final =[
                'seo_title'=>$request->get('seo_title'),
                'seo_meta'=>$request->get('seo_meta'),
                'seo_description'=>$request->get('seo_description')
            ];

            $meta_insert = new MetaArticle ();
            $meta_insert->meta_key = 'seo';
            $meta_insert->meta_value = json_encode($meta_final);
            $meta_insert->article_id = $article->id;
            $meta_insert->save();

            //Insert Meta Seo
            // $job = new \App\Jobs\JobCacheArray($article->id, 'recipe', []);
            // $this->dispatch($job);
            // $job2 = new \App\Jobs\CacheRecipes();
            // $this->dispatch($job2);

    return json_encode(['status' => 'success', 'msg' => 'Lưu bài viết thành công']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
     }

    public function getEdit($article_id) {
        $this->authorize('EditArticle');
        try {
           $category = Category::where('type','Category')->get();
           $article = Article::findOrFail($article_id);
           $list_tag  = MetaArticle::where('meta_key','tag')->where('article_id',$article_id)->first();
           $list_related  = MetaArticle::where('meta_key','related')->where('article_id',$article_id)->first();
           $seo = MetaArticle::where('meta_key','seo')->where('article_id',$article_id)->first();
           return view('childs.recipe.edit',compact('category','article','list_tag','list_related','seo'));
        } catch (Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    public function postEdit($id,Request $request) {
        $this->authorize('EditArticle');
        try {
            $meta_data = \Input::only(['title', 'youtube', 'description', 'publish_date','publish_time','status','seo_title', 'seo_meta', 'seo_description', 'type_article', 'tags', 'related','thumbnail']);
           
            $article = Article::findOrFail($id);
            $article->title = $request->get('title');
            $article->slug = str_slug($request->get('title'));
            $article->link_video = $request->get('youtube');
            $article->description = $request->get('description');
            
            $thumbnail = !empty($request->get('thumbnail')) ? $request->get('thumbnail') : '';
            if (!empty($thumbnail) && (str_contains($thumbnail, 'http://') == false)) {
                $this->resizeImage($thumbnail, 500, 400);
                $article->thumbnail = $thumbnail;
            }else{
                $article->thumbnail = $thumbnail;
            }

            if (\Input::get('status') == '') {
                $article->status = 'schedule';
            }else{
                $article->status = $request->get('status');
            }

            if (\Input::get('status') == 'publish') {
                $article->published_at = date('Y-m-d H:i:s');
            }else{
                $datetime_publish = date('Y-m-d H:i', strtotime(\Input::get('publish_date') . ' ' . \Input::get('publish_time')));
                $article->published_at = $datetime_publish;
            } 

            $article->creator = \Auth::user()->email;
            $article->update();
            
            //insert cotegory
            $check_cate_list = '';
            $cate_list = explode(',',$request->get('category'));
            $count_cate = ArticleCategory::select('article_id','category_id')->where('article_id',$id)->count();
            for($i=0; $i< $count_cate; $i++){
                $check_cate = ArticleCategory::select('category_id')->where('article_id',$id)->first();
                if($i==0){
                    $check_cate_list = $check_cate->category_id;
                }else{
                    $check_cate_list = ','.$check_cate->category_id; 
                }
            }
            if(trim($check_cate_list) != trim($request->get('category'))){
            // for($i=0; $i< $count_cate; $i++){
            //     $del = ArticleCategory::where('article_id',$id)->first();
            //     $del->delete();
            // }
            if(count($cate_list)>0){
            for($i=0; $i<count($cate_list); $i++){
                $del = ArticleCategory::where('article_id',$id)->first();
                if(count($del)==0){
                    $cate = new ArticleCategory;
                    $cate->category_id = $cate_list[$i];
                    $cate->article_id = $id;
                    $cate->save();
                }
            }
            } 
            }

           if($request->get('list_tag') != ''){
            $list_tag= explode('||',$request->get('list_tag'));
            $meta_tag =[];
            for($i=0; $i<count($list_tag); $i++){
                    $slug = str_slug($list_tag[$i]);
                    $cate = Category::where('type','tag')->select('id','title')->where('slug',$slug)->first();
                    if(!isset($cate)){
                        $cat = new Category;
                        $cat->title = $list_tag[$i];
                        $cat->slug = $slug;
                        $cat->desc = $list_tag[$i];
                        $cat->type = 'tag';
                        $cat->status = 1;   
                        $cat->save();
                        $meta_tag[] = [$cat->id => $cat->title];
                    }else{
                        $meta_tag[] = [$cate->id => $cate->title];
                    }
               
             }  
                $meta_tags = MetaArticle::where('article_id',$id)->where('meta_key','tag')->first();
                if(count($meta_tags)>0){
                    $meta_tags->meta_value = json_encode($meta_tag);
                    $meta_tags->update();
                }else{
                    $tag = new MetaArticle;
                    $tag->meta_key = 'tag';
                    $tag->meta_value = json_encode($meta_tag);
                    $tag->article_id = $id;
                    $tag->save(); 
                }
            }else{
                $meta_tags = MetaArticle::where('article_id',$id)->where('meta_key','tag')->first();
                if(count($meta_tags)>0){
                    $meta_tags->meta_value = '';
                    $meta_tags->update();
                }
            }

            if($request->get('list_related') != ''){
             $list_related= explode('||',$request->get('list_related'));
             $meta_related = [];
             $check = 0;
            for($i=0; $i<count($list_related); $i++){
                $slug = str_slug($list_related[$i]);
                $related_id = Article::select('id','title')->where('slug',$slug)->first();
                if(isset($related_id)){
                    $meta_related[] = [$related_id->id => $related_id->title]; $check =1; 
                }
                 }

                $related_telate = MetaArticle::where('article_id',$id)->where('meta_key','related')->first();
                if($check ==1){
                if(count($related_telate)>0){
                    $related_telate->meta_value = json_encode($meta_related);
                    $related_telate->update();
                }else{
                    $related = new MetaArticle;
                    $related->meta_key = 'related';
                    $related->meta_value = json_encode($meta_related);
                    $related->article_id = $id;
                    $related->save(); 
                }
                }
            }else{
                $related_telate = MetaArticle::where('article_id',$id)->where('meta_key','related')->first();
                if(count($related_telate)>0){
                    $related_telate->meta_value = '';
                    $related_telate->update();  }
            }


            //insert meta key
            $meta_final = [
                'seo_title'=>$request->get('seo_title'),
                'seo_meta'=>$request->get('seo_meta'),
                'seo_description'=>$request->get('seo_description'),
            ];

            $meta_insert1 = MetaArticle::select('id')->where('meta_key','seo')->where('article_id',$id)->first();
            if (count($meta_insert1)>0) {
                $meta_insert = MetaArticle::findOrFail($meta_insert1->id);
            }else{
                $meta_insert = new MetaArticle;
            }

            $meta_insert->meta_key = 'seo';
            $meta_insert->meta_value = json_encode($meta_final);
            $meta_insert->article_id = $id;
            $meta_insert->save();

           return json_encode(['status' => 'success', 'msg' => 'Sửa thành công']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postPublish() {
        $this->authorize('EditArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $article = Article::find((int) $article_id);
            $article->status = 'publish';
            $article->published_at = date('Y-m-d H:i:s');
            $article->save();
            $this->_post->getById($article->id);
            $job = new \App\Jobs\JobCacheArray($article_id, 'recipe', []);
            $this->dispatch($job);
            $job2 = new \App\Jobs\CacheRecipes();
            $this->dispatch($job2);
            return json_encode(['status' => 'success', 'msg' => 'Publish successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postTrash() {
        $this->authorize('EditArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $article = Article::find((int) $article_id);
            $article->delete();
            $this->_post->getById($article->id);
            $job = new \App\Jobs\JobCacheArray($article_id, 'recipe', []);
            $this->dispatch($job);
            $job2 = new \App\Jobs\CacheRecipes();
            $this->dispatch($job2);
            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postUntrash() {
        $this->authorize('EditArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            Article::withTrashed()->where('id', (int) $article_id)->restore();
            $job = new \App\Jobs\JobCacheArray($article_id, 'recipe', []);
            $this->dispatch($job);
            $job2 = new \App\Jobs\CacheRecipes();
            $this->dispatch($job2);
            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postVerify() {
        $this->authorize('EditArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $article = Article::find((int) $article_id);
            $article->status = 'pending';
            $article->published_at = null;
            $article->save();
            $this->_post->getById($article->id);
            $job = new \App\Jobs\JobCacheArray($article_id, 'recipe', []);
            $this->dispatch($job);
            $job2 = new \App\Jobs\CacheRecipes();
            $this->dispatch($job2);
            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postUnverify() {
        $this->authorize('EditArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $article = Article::find((int) $article_id);
            $article->status = 'draft';
            $article->published_at = null;
            $article->save();
            $this->_post->getById($article->id);
            $job = new \App\Jobs\JobCacheArray($article_id, 'recipe', []);
            $this->dispatch($job);
            $job2 = new \App\Jobs\CacheRecipes();
            $this->dispatch($job2);
            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postDraft() {
        $this->authorize('EditArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $article = Article::find((int) $article_id);
            $article->status = 'draft';
            $article->published_at = null;
            $article->save();
            $this->_post->getById($article->id);

            $job = new \App\Jobs\JobCacheArray($article_id, 'recipe', []);
            $this->dispatch($job);

            $job2 = new \App\Jobs\CacheRecipes();
            $this->dispatch($job2);
            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

}
