<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');

Route::group(['middleware' => 'auth:api', 'prefix' => ''], function(){
    Route::post('details', 'API\UserController@details');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'news'], function(){
    Route::post('', 'API\NewsController@create')->name('news.create');
    Route::put('{news_id}', 'API\NewsController@update');
    Route::delete('{news_id}', 'API\NewsController@delete');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'comment'], function(){
    Route::post('', 'API\CommentsController@create');
    Route::get('', 'API\CommentsController@list');
    Route::put('{comment_id}', 'API\CommentsController@update');
    Route::delete('{comment_id}', 'API\CommentsController@delete');
});

Route::get('news', 'API\NewsController@list');
