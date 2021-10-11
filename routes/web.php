<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::group(['middleware' => ['auth']], function () {
//     Route::get('/agora-chat', 'AgoraVideoController@index');
//     Route::post('/agora/token', 'AgoraVideoController@token');
//     Route::post('/agora/call-user', 'AgoraVideoController@callUser');
// });

Route::group(['middleware' => ['auth']], function () {
    Route::get('/agora-chat/{target_user_id}', 'AgoraVideoController@index');
    Route::post('/agora/token', 'AgoraVideoController@token');
    Route::post('/agora/call-user', 'AgoraVideoController@callUser');
});


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
