<?php

Route::get('/', 'Auth\AuthController@getLogin');
Route::post('/', 'Auth\AuthController@postLogin');
Route::get('logout', 'Auth\AuthController@getLogout');

Route::group(['middleware' => ['auth']], function () {

    Route::get('home', 'HomeController@getIndex');
    Route::group(['prefix' => 'media'], function () {
        Route::controller('article', 'ArticleController', ['postCreate' => 'createArticle', 'postEdit' => 'editArticle', ]);
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
});


Route::any('upload-image', function () {
    $new = new App\Helpers\UploadHandler();
});

Route::resource('delete-image', 'HomeController');
Route::post('oauth/access_token', function () {
    return Response::json(Authorizer::issueAccessToken());
});

