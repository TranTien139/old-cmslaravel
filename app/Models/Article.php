<?php

namespace App\Models;

use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Watson\Rememberable\Rememberable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model implements AuthorizableContract
{

    use Rememberable;

    use Authorizable;

    use SoftDeletes;

    protected $table = 'article';
    protected $dates = ['deleted_at'];
    
    function articleCategory()
    {
        return $this->belongsToMany('App\Models\Category', 'article_category', 'article_id', 'category_id');
    }
    function getUser()
    {
        return $this->hasOne('App\Models\User', 'email', 'creator');
    }

    function getVideo()
    {
        return $this->hasOne('App\Models\Video', 'id', 'video_id');
    }
}
