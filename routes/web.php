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

Route::get( '/about',   'HomeController@about')  ->name('about');
Route::get( '/contact', 'HomeController@contact')->name('contact');
Route::get( '/privacy', 'HomeController@privacy')->name('privacy');
Route::get( '/terms',   'HomeController@terms')  ->name('terms');

Route::get( '/news',            'DashboardController@news')->name('news');
Route::get( '/calendar',        'DashboardController@calendar') ->name('calendar');
Route::get( '/calendar/iframe', 'DashboardController@calendarIframe') ->name('calendarIframe');
Route::get( '/roster',          'DashboardController@roster')   ->name('roster');

Route::get( '/resources',        'ContentController@index')->name('contentIndex');
Route::get( '/resources/{slug}', 'ContentController@show')->name('showContent');

Route::group([
        'middleware' => 'acl',
        'is'         => 'admin|guild_master|officer|raid_leader|class_leader|raider',
    ], function () {
    Route::post('/updateContent/{id?}', 'ContentController@update')->where('id', '[0-9]+')->name('updateContent');
    Route::post('/removeContent/{id}',  'ContentController@remove')->where('id', '[0-9]+')->name('removeContent');
});

Route::post('/{id}/updateAll',          'ProfileController@submit')->where('id', '[0-9]+')            ->name('updateUser');
Route::post('/{id}/updatePersonalNote', 'ProfileController@submitPersonalNote')->where('id', '[0-9]+')->name('updateUserPersonalNote');

Route::get( '/ban/{id}', [
        'uses'       => 'ProfileController@ban',
        'middleware' => 'acl',
        'is'         => 'admin|guild_master|officer|raider',
    ])->where('id', '[0-9]+')               ->name('banUser');


Route::group(['prefix' => '{id}'], function () {
    Route::get( '/',            'ProfileController@findById')->where('id', '[0-9]+')->name('findUserById');
    Route::get( '/{username?}', 'ProfileController@showUser')->where('id', '[0-9]+')->name('showUser');
});

Route::group([
        'prefix'     => 'guild',
        'middleware' => 'acl',
        'is'         => 'admin|guild_master|officer|raider',
    ], function () {
    Route::get( '/raids',     'RaidsController@raids')    ->name('guild.raids');

    Route::get( '/roles',     'RolesController@roles')    ->name('guild.roles');
    Route::get( '/syncRoles', 'RolesController@syncRoles')->name('guild.syncRoles');

    // Can't get the permissions working right now (2019-12-02), so I'm disabling this.
    // Route::get( '/permissions', 'PermissionsController@permissions')->name('guild.permissions');
    // Route::get( '/addPermissions', 'PermissionsController@addPermissions')->name('guild.addPermissions');
});

Route::get( '/{username}',      'ProfileController@findByUsername')->name('findUserByUsername');
