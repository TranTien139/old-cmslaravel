<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Repositories\Article\ArticleInterface;
use Illuminate\Console\Command;
use App\Models\Category;

class CacheHome extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:home';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'IMPORT CACHE REDIS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    function __construct(ArticleInterface $articleInterface)
    {
        parent::__construct();
        $this->_article = $articleInterface;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = [];

        $category_clip_hot = null;
        $article_clip_hot = null;

        $category_clip_hot = Category::find(CLIP_HOT);
        $article_clip_hot = $this->_article->getByCategory($category_clip_hot);

        $category_hai_nuoc_ngoai = Category::find(HAI_NUOC_NGOAI);
        $article_hai_nuoc_ngoai = $this->_article->getByCategory($category_hai_nuoc_ngoai);

        $category_hai_viet_nam = Category::find(DANH_HAI_VIET_NAM);
        $article_hai_viet_nam = $this->_article->getByCategory($category_hai_viet_nam);

        $data = [
            'category_clip_hot' => $category_clip_hot,
            'category_hai_nuoc_ngoai' => $category_hai_nuoc_ngoai,
            'category_hai_viet_nam' => $category_hai_viet_nam,
            'article_clip_hot' => $article_clip_hot,
            'article_hai_nuoc_ngoai' => $article_hai_nuoc_ngoai,
            'article_hai_viet_nam' => $article_hai_viet_nam
        ];

        $view = view('cacheView.home.home')->with($data);
        \Cache::forever('cache_home', (string)$view);
    }
}
