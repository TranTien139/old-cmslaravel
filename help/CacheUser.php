<?php
// LƯU QUAN HỆ 1 - 1  User ARTICLE

function UserArticle($article_id = 113, $user_id = null, $score = 0)
{
    if ($user_id != null) {
        $redis = new Redis();
        $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
        $redis->zAdd('user_article_' . $user_id, $score, $article_id);
    };

}

function RemoveIndexUserArticle($article)
{
    $user_id = $article->getUser->id;
    @DeleteZKeyIndex('user_article_' . $user_id, $article->id);
}

