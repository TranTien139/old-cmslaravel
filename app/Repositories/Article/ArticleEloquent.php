<?php
namespace App\Repositories\Article;

use App\Models\Articles;

class ArticleEloquent implements ArticleInterface
{

    function getById($id)
    {
        //Check Cache
        if (\Cache::has('article_' . $id)) {
            $article = \Cache::get('article_' . $id);
            $type = isset($article->type) ? $article->type : '';
            if ($type == 'Review') {
                return $article;
            } else {
                return null;
            }
        }
        $article = Article::where('article.type', 'Review')
            ->where('status', 'publish')
            ->where('id', $id)
            ->first();

        return $article;
    }

    function getWhereInArticle($data_id, $take = null)
    {
        $i = 1;
        $articles = [];
        foreach ($data_id as $items) {
            $article = $this->getById($items);
            if ($article != null) {
                $articles [] = $article;
                if ($take != null && $i == 5) {
                    return $articles;
                }
            }
            $i++;
        }
        return $articles;
    }

    function getByCategory($category)
    {
        if (isset($category->categoryArticle)) {
            return $category->categoryArticle()->where(function ($articles) {
                $articles->where('status', 'publish');
            })->orderBy('published_at', 'desc')->orderBy('id', 'desc')->paginate(8);
        } else {
            return Articles::orderBy('id', 'desc')->paginate(8);
        }

    }

    function getNew($take = null, $category_id = null, $order_by = null, $search = null, $creator = null, $like = false)
    {
        $articles = Article::
        with('getWard', 'articleAdress', 'articleRating', 'getRateArticle', 'getUser')
            ->where('article.type', 'Review')
            ->where('article.status', 'publish')
            ->orderBy('article.published_at', 'desc')
            ->orderBy('article.id', 'desc');
        $articles = $articles->select('article.*',
            'meta_article.meta_value',
            'ward.name as ward_name',
            'district.name as district_name',
            'district.type as district_type',
            'province.name as province_name',
            'province.type as province_type');
        $articles = $articles->join('meta_article', 'article.id', '=', 'meta_article.article_id')
            ->join('ward', 'meta_article.meta_value', '=', 'ward.wardid')
            ->join('district', 'ward.districtid', '=', 'district.districtid')
            ->join('province', 'district.provinceid', '=', 'province.provinceid')
            ->where(function ($query) {
                $query->where('meta_article.meta_key', 'review_ward');
                $query->OrWhere('meta_article.meta_key', 'relation_category');
            })
            ->where('meta_article.meta_value', '!=', '');
        if ($creator != null) {
            $articles = $articles->where('article.creator', $creator);
        }
        if ($like != false) {
            $articles = $articles
                ->join('meta_article_fe', 'article.id', '=', 'meta_article_fe.article_id')
                ->where('meta_article_fe.meta_value', "$like")
                ->where('meta_article_fe.meta_key', 'like', 'like_article%');
        }
        if ($search != null) {
            $articles->where(
                function ($query) use ($search) {
                    $query->where('article.title', 'like', '% ' . $search . ' %');
                    $query->Orwhere('article.title', 'like', '%' . $search . '%');
                    $query->Orwhere('article.title', 'like', $search . '%');
                    $query->Orwhere('article.title', 'like', '%' . $search);
                }
            );
        }
        if ($category_id != null) {
            $articles = $articles->where('meta_article.meta_value', $category_id);
        }
        $articles = $articles->groupBy('article.id');
        if ($take != null) {
            $articles = $articles->paginate($take);
        } else {
            $articles = $articles->paginate(16);
        }
        return $articles;

    }

    function getMostView($take)
    {
        $review = Article::with('getWard', 'articleAdress', 'articleRating', 'getRateArticle', 'getUser', 'articleLike')
            ->selectRaw('article.id, CONVERT( meta_article_fe.meta_value, UNSIGNED INTEGER ) as num ');
        $review = $review
            ->join('meta_article', 'article.id', '=', 'meta_article.article_id')
            ->join('ward', 'meta_article.meta_value', '=', 'ward.wardid')
            ->join('meta_article_fe', 'meta_article_fe.article_id', '=', 'article.id')
            ->where('meta_article_fe.meta_key', 'view_article')
            ->join('district', 'ward.districtid', '=', 'district.districtid')
            ->join('province', 'district.provinceid', '=', 'province.provinceid')
            ->where('meta_article.meta_key', 'review_ward')
            ->where('meta_article.meta_value', '!=', '');
        $review = $review->where('article.type', 'Review')->where(function ($review) {
            $review->where('article.status', 'publish');
        });
        $review = $review->orderBy('num', 'desc')->orderBy('article.id', 'desc');
        $review = $review->skip(0)->take(4)->get();
        foreach ($review as $article) {
            $data[] = $this->getById($article->id);
        }
        return $data;
    }

    function getRelate($article)
    {
        if (isset($article->related) && count($article->related->meta_value) > 0) {
            $relate = @json_decode($article->related->meta_value);
            if (isset($relate)) {
                foreach ($relate as $item) {
                    foreach ($item as $k => $v) {
                        $related_id[] = $k;
                    }
                }
            }
            if (!empty($related_id)) {
                //GOI REPOSITORY LAY RA BAI VIET LIEN QUAN + CACHE
                $relateds = $this->getWhereInArticle($related_id);
            } else {
                $relateds = $this->getMostView(4);
            }
            return $relateds;
        } else {
            $relateds = $this->getMostView(4);
        }

    }
}