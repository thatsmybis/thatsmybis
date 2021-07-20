<?php

/*
|--------------------------------------------------------------------------
| Scratch Routes
|--------------------------------------------------------------------------
|
| This is where we store all routes that are no longer actively used within
| the application but want to keep around for debugging / local dev purposes.
|
*/

// Password Reset routes:
Route::get( 'password/reset','Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email','Auth\ForgotPasswordController@sendResetLinkEmail') ->name('password.email');
Route::get( 'password/reset/{token}','Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset','Auth\ResetPasswordController@reset');

// Email Validation routes
Route::get('email/verify','Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}','Auth\VerificationController@verify')->name('verification.verify');
Route::get('email/resend','Auth\VerificationController@resend')->name('verification.resend');

 Route::group(['prefix' => 'item'], function () {
     Route::get( '/{item_id}/{slug?}', 'ItemController@show')->name('item.show');
 });

Route::get( '/about','HomeController@about')->name('about');
Route::get( '/contact','HomeController@contact')->name('contact');

Route::group([
    'middleware' => ['seeUser', 'checkGuildPermissions'],
    'prefix'     => '{guildId}/{guildSlug}',
    'where'      => ['guildId' => '[0-9]+'],
], function () {
    Route::get('/news','DashboardController@news')->name('guild.news');
    Route::get('/calendar','DashboardController@calendar')->name('guild.calendar');
    Route::get('/calendar/iframe','DashboardController@calendarIframe')->name('guild.calendarIframe');

    Route::get( '/resources','ContentController@index')->name('contentIndex');
    Route::get( '/resources/{slug}','ContentController@show')->name('showContent');
    Route::get( '/posts/{slug}','ContentController@show')->name('showPost');
});

Route::group([
    'middleware' => 'acl',
    'is'         => env('PERMISSION_CLASS_LEADER'),
], function () {
Route::post('/updateContent/{id?}','ContentController@update')->where('id', '[0-9]+')->name('updateContent');
Route::post('/removeContent/{id}','ContentController@remove')->where('id', '[0-9]+')->name('removeContent');
});
