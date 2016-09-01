<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
	return view('welcome');
});

Auth::routes();

Route::get('/pinterest/callback', 'PinterestController@callback');

Route::get('/home', 'HomeController@index');
Route::get('/reset', 'HomeController@reset');

Route::get('/board/{board}', 'HomeController@board');

Route::get('/importboards', 'HomeController@importboards');
Route::get('/importpins', 'HomeController@importpins');
