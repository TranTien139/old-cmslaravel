<?php
namespace App\Http\Controllers\TraitController;

use Illuminate\Http\Request;
use App\Jobs\ActionContent;
use App\Jobs\ReleaseContent;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\Article;
use App\Models\Category ;
use App\Http\Requests;
use App\Models\Tags;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Models\ArticleTag;

trait Tag
{

    function getTag()
    {   $array = array();
        $tag = $this->_request->get('term');
        $find_tag = Category::where('title', 'like', "%$tag%")->where('type','tag')->take(5)->get();
        foreach ($find_tag as $items) {
            $array[] = $items->title;
        }
        return $array;
    }
    function getRelated(){
        $array = array();
        $tag = $this->_request->get('term');
        $find_tag = Article::where('title', 'like', "%$tag%")->take(5)->get();
        foreach ($find_tag as $items) {
            $array[] = str_replace(',' , '\,' , $items->title);
        }
        return $array;
    }
}