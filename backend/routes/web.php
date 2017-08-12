<?php

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
Route::get('/mobile', 'MobileController@send');
Route::get('/weight/{timestamp}/{value}', 'DataController@weight');
Route::get('/check/{timestamp}', 'DataController@check');
Route::get('api/transactions', 'DataController@transactions');
Route::get('api/quantities', 'DataController@quantities');
Route::get('api/weights', 'DataController@weights');
Route::get('api/times', 'DataController@times');
Route::get('/', function () {
    return view('welcome');
});
