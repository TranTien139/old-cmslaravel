<?php

namespace App\Http\Controllers\TraitController;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\MetaArticle;
use App\Models\Article;
use Illuminate\Support\Facades\Input;
use App\Repositories\Post\PostRepository;

trait ArticleDetail
{
    public function __construct(Request $request, PostRepository $article_interface)
    {
        $this->_request = $request;
        $this->_post = $article_interface;
    }

    public function postPublish()
    {
        $this->authorize('PublishArticle');
        try {
            $email = auth()->user()->email;
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $article = Article::find((int)$article_id);
            $article->status = 'publish';
            $article->published_at = date('Y-m-d H:i:s');
            $article->approve_by = $email;
            $article->save();
            $this->_cache->getById($article->id);
            @AddIndexArticle($article);
            @AddArtcileToTags($article);
            @UserArticle($article->id, $article->getUser->id, strtotime($article->published_at));
            return json_encode(['status' => 'success', 'msg' => 'Publish successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postTrash()
    {
        $this->authorize('TrashArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $article = Article::find((int)$article_id);
            @RemoveIndexArticle($article);
            @RemoveIndexUserArticle($article);
            $article->delete();
            $this->_cache->getById($article->id);
            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postUntrash()
    {
        $this->authorize('TrashArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            Article::withTrashed()->where('id', (int)$article_id)->restore();
            $this->_cache->getById($article->id);
            @RemoveIndexArticle($article);
            @RemoveIndexUserArticle($article);
            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postVerify()
    {
        $this->authorize('EditArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $article = Article::findOrFail((int)$article_id);
            $article->status = 'pending';
            $article->published_at = null;
            $article->save();
            $this->_cache->getById($article->id);
            @RemoveIndexArticle($article);
            @RemoveIndexUserArticle($article);
            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postUnverify()
    {
        $this->authorize('EditArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $article = Article::find((int)$article_id);
            $article->status = 'draft';
            $article->published_at = null;
            $article->save();
            $this->_cache->getById($article->id);
            @RemoveIndexArticle($article);
            @RemoveIndexUserArticle($article);
            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postDraft()
    {
        $this->authorize('EditArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $article = Article::find((int)$article_id);
            $article->status = 'draft';
            $article->published_at = null;
            $article->save();
            $this->_cache->getById($article->id);
            @RemoveIndexArticle($article);
            @RemoveIndexUserArticle($article);
            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

}

?>
