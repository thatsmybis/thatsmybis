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

Route::get('/home', function () {request()->session()->reflash(); return redirect()->route('home');}); // Laravel's framework directs to '/home' in several scenarios...
Route::get( '/',    'HomeController@index')->name('home');

// Authentication routes:
Route::get( 'login',  'Auth\LoginController@showLoginForm')->name('login');
Route::post('login',  'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')       ->name('logout');
// Registration routes:
Route::get( 'register',             'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register',             'Auth\RegisterController@register');
// Password Reset routes:
Route::get( 'password/reset',         'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email',         'Auth\ForgotPasswordController@sendResetLinkEmail') ->name('password.email');
Route::get( 'password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')       ->name('password.reset');
Route::post('password/reset',         'Auth\ResetPasswordController@reset');
// Email Validation routes
Route::get('email/verify',      'Auth\VerificationController@show')   ->name('verification.notice');
Route::get('email/verify/{id}', 'Auth\VerificationController@verify') ->name('verification.verify');
Route::get('email/resend',      'Auth\VerificationController@resend') ->name('verification.resend');
Route::group(['prefix' => 'auth'], function () {
    // Discord sign-in
    Route::get('/discord',          'Auth\LoginController@redirectToDiscord')    ->name('discordLogin');
    Route::get('/discord/callback', 'Auth\LoginController@handleDiscordCallback');
});

Route::get( '/dashboard', 'HomeController@dashboard')    ->name('dashboard');

Route::get( '/about', 'HomeController@about')    ->name('about');
Route::get( '/contact', 'HomeController@contact')->name('contact');
Route::get( '/privacy', 'HomeController@privacy')->name('privacy');
Route::get( '/terms',   'HomeController@terms')  ->name('terms');
