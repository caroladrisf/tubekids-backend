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
    Route::post('users/{user}/sms', 'UserController@sendSMS');
    Route::delete('users/session', 'UserController@logout')->name('logout');
    
    Route::get('profiles', 'ProfileController@index');
    Route::post('profiles', 'ProfileController@store');
    Route::get('profiles/{id}', 'ProfileController@show');
    Route::put('profiles/{id}', 'ProfileController@update');
    Route::delete('profiles/{id}', 'ProfileController@destroy');
    
});

Route::post('users/{user}/confirmation-email', 'MailController@sendConfirmationEmail');
Route::get('users/{user}/confirm', 'UserController@confirmEmailAddress');
