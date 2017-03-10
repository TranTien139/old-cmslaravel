<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Videos extends Model
{
    protected $table = 'video';

    protected $rememberFor = 10;
    protected $rememberCacheTag = 'table_video';
}
