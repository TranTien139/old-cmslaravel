<?php
namespace App\Repositories\Article;
interface ArticleInterface
{

    function getById($id);

    function getByCategory($category);

    function getMostView($take);

    function getNew($take = null, $category_id = null, $order_by = null, $search = null, $creator = null, $like = false);

    function getRelate($article);

}