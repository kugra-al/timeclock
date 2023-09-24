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

// Authentication Routes...
$this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
$this->post('login', 'Auth\LoginController@login');
$this->post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
//$this->get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
//$this->post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
$this->post('password/reset', 'Auth\ResetPasswordController@reset');

Route::get('/', 'FrontController@index');
Route::post('/ajax/front', 'FrontController@ajaxRequestPost');

Route::get('/admin','AdminController@index')->middleware('auth');
Route::post('/ajax/back','AdminController@ajaxRequestPost')->middleware('auth');

Route::resource('/admin/staff', 'StaffController')->only([
	'index','show','create','edit','store','update','destroy'
])->middleware('auth');

Route::resource('/admin/clock', 'ClockController')->only([
	'index','show','create','edit','store','update','destroy','csv'
])->middleware('auth');

Route::resource('/admin/users', 'UserController')->only([
	'index','show','create','edit','store','update','destroy'
])->middleware('auth');





Route::get('/home', 'AdminController@index')->name('home')->middleware('auth');
