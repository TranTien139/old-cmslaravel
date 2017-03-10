<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Remember;
use Watson\Rememberable\Rememberable;

class Articles extends Model
{
    protected $table = 'article';
    use Rememberable;
    use SoftDeletes;

    function getUser()
    {
        return $this->hasOne('App\User', 'email', 'creator');
    }

    function getVideo()
    {
        return $this->hasOne('App\Models\Videos', 'id', 'video_id');
    }
}
