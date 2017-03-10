<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notify extends Model
{
    protected $table = 'notify';

    function getUser()
    {
        return $this->hasOne('App\Models\User', 'email', 'creator');
    }

    function getArticle()
    {
        return $this->hasOne('App\Models\Article', 'id', 'article_id')->orderBy('id', 'desc');
    }
}
