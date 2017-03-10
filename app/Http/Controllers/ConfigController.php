<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Storage;

class ConfigController extends Controller
{
    function postCreate()
    {
        $data = request()->except('_token');
        $cache_key = json_encode($data);
        CreateFile($data);
        \Cache::forever('config_web_video', $cache_key);
        return json_encode(['status' => 'success', 'msg' => 'Post Successfully']);
    }

    function getCreate()
    {
        return view('childs.configs.config');
    }

    function getIndex(){
    try{
      $content = Storage::disk('local')->get('logs.html');
      return view('childs.log.log',compact('content'));
    }catch(Exception $e){
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }  
    }
}
