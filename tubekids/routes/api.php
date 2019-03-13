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

Route::post('users/session', 'UserController@login')->name('login');
Route::post('users', 'UserController@register')->name('register');

Route::middleware(['auth.jwt'])->group(function(){
    Route::delete('users/session', 'UserController@logout')->name('logout');

});