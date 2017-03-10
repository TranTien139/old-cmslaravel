<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

Route::get('/', 'Auth\AuthController@getLogin');
Route::post('/', 'Auth\AuthController@postLogin');
Route::get('logout', 'Auth\AuthController@getLogout');

Route::group(['middleware' => ['auth']], function () {

    Route::get('home', 'HomeController@getIndex');
    Route::group(['prefix' => 'media'], function () {
        Route::controller('article', 'ArticleController', [
            'postCreate' => 'createArticle',
            'postEdit' => 'editArticle',
        ]);
        Route::controller('blog', 'BlogsController');
        Route::controller('category', 'CategoryController');
        Route::controller('built-top', 'BuilttopController');
    });
    Route::controller('article', 'ArticleController', [
        'postCreate' => 'createArticle', 'postEdit' => 'editArticle',
    ]);
    Route::controller('category', 'CategoryController');
    Route::controller('user', 'UserController');
    Route::get('quan-tri-vien', 'UserController@getQuanTriVien');
    Route::get('thanh-vien', 'UserController@getThanhVien');
    Route::get('profile', 'UserController@getProfile');
    Route::controller('questions', 'QuestionsController');
    Route::controller('video', 'VideoController');
    Route::controller('gallery', 'GalleryController');
    Route::get('member/article', 'ArticleController@getArticleByMember');
    Route::controller('events', 'EventsController');
});


Route::get('cache-article', function () {
    $a = \App\Models\Article::where('id', '>=', request()->get('turn1'))->where('id', '<=', request()->get('turn2'))->get();
    $cache = new \App\Cache\Article\ArticleCache();
    foreach ($a as $article) {
        $cache->getById($article->id);
    }
});


Route::resource('api', 'ApiController');
Route::controller('api-user', '\App\Http\Controllers\ApiProcess\UserProcess');
Route::controller('api-collection', '\App\Http\Controllers\ApiProcess\CollectionProcess');
Route::controller('api-question', '\App\Http\Controllers\ApiProcess\QuestionProcess');
//Route::controller('api-article' , '\App\Http\Controllers\ApiProcess\ArticleProcess') ;
Route::any('upload-image', function () {
    $new = new App\Helpers\UploadHandler();
});
Route::resource('delete-image', 'HomeController');
Route::post('oauth/access_token', function () {
    return Response::json(Authorizer::issueAccessToken());
});

