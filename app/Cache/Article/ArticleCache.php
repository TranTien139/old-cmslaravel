<?php
namespace App\Cache\Article;
class  ArticleCache implements ArticleInterface
{
    function CacheDetail($article)
    {
        if (isset ($article->getUser)) {
            $article->getUser = $article->getUser;
        }
        if (isset ($article->getVideo)) {
            $article->getVideo = $article->getVideo;
        }
        return $article;
    }

    function getById($id)
    {
        //Check Cache
        $article = \App\Models\Articles::whereId($id)
            ->first();
        $article = $this->CacheDetail($article);

        \Cache::forget('article_' . $id, $article);
        \Cache::put('article_' . $id, $article , 43200);
        return $article;

    }
}