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
Route::get( '/faq',     'HomeController@faq')    ->name('faq');
Route::get( '/privacy', 'HomeController@privacy')->name('privacy');
Route::get( '/terms',   'HomeController@terms')  ->name('terms');

Route::get( '/register-guild', 'GuildController@showRegister')->name('guild.showRegister');
Route::post('/submit-guild',   'GuildController@register')    ->name('guild.register');

// Route::group(['prefix' => 'item'], function () {
//     Route::get( '/{item_id}/{slug?}', 'ItemController@show')->name('item.show');
// });

Route::group([
        'middleware' => ['seeUser', 'checkGuildPermissions'],
        'prefix'     => '{guildSlug}'
    ], function () {

    Route::get( '/news',            'DashboardController@news')          ->name('guild.news');
    Route::get( '/calendar',        'DashboardController@calendar')      ->name('guild.calendar');
    Route::get( '/calendar/iframe', 'DashboardController@calendarIframe')->name('guild.calendarIframe');

    Route::group(['prefix' => 'c'], function () {
        Route::get( '/create',      'CharacterController@showCreate')->name('character.showCreate');
        Route::post('/create',      'CharacterController@create')    ->name('character.create');
        Route::get( '/{name}/edit', 'CharacterController@edit')      ->name('character.edit');
        Route::get( '/{name}/loot', 'CharacterController@loot')      ->name('character.loot');
        Route::post('/update',      'CharacterController@update')    ->name('character.update');
        Route::post('/loot/update', 'CharacterController@updateLoot')->name('character.updateLoot');
        Route::post('/note/update', 'CharacterController@updateNote')->name('character.updateNote');
        Route::get( '/{name}',      'CharacterController@show')      ->name('character.show');
    });

    Route::get( '/loot/{instanceSlug}',    'ItemController@listWithGuild')->name('guild.item.list');

    Route::group(['prefix' => 'i'], function () {
        Route::get( '/{item_id}/{slug?}', 'ItemController@showWithGuild')->name('guild.item.show');
        Route::post('/note/update',       'ItemController@updateNote')   ->name('guild.item.updateNote');
    });

    Route::group(['prefix' => 'u'], function () {
        Route::get( '/{username}/edit', 'MemberController@edit')      ->name('member.edit');
        Route::post('/update',          'MemberController@update')    ->name('member.update');
        Route::post('/note/update',     'MemberController@updateNote')->name('member.updateNote');
        Route::get( '/{username}',      'MemberController@show')      ->name('member.show');
    });

    Route::get( '/resources',        'ContentController@index')->name('contentIndex');
    Route::get( '/resources/{slug}', 'ContentController@show') ->name('showContent');
    Route::get( '/posts/{slug}',     'ContentController@show') ->name('showPost');

    Route::get( '/roster',          'DashboardController@roster')->name('guild.roster');

    Route::get( '/raid-time', 'ItemController@massInput')      ->name('item.massInput');
    Route::post('/raid-time', 'ItemController@submitMassInput')->name('item.massInput.submit');

    Route::group([
        // 'middleware' => 'acl',
        // 'is'         => 'admin|guild_master|officer|raider',
    ], function () {
        Route::group(['prefix' => 'raid'], function () {
            Route::get( '/',               'RaidController@raids')        ->name('guild.raids');
            Route::get( '/create',         'RaidController@edit')         ->name('guild.raid.create');
            Route::get( '/edit/{id?}',     'RaidController@edit')         ->name('guild.raid.edit');
            Route::post('/toggle-disable', 'RaidController@toggleDisable')->name('guild.raid.toggleDisable');
            Route::post('/update',         'RaidController@update')       ->name('guild.raid.update');
            Route::post('/',               'RaidController@create')       ->name('guild.raid.create');
        });

        Route::get( '/roles',     'RoleController@roles')    ->name('guild.roles');
        Route::get( '/syncRoles', 'RoleController@syncRoles')->name('guild.syncRoles');

        Route::get( '/settings',  'GuildController@settings')->name('guild.settings');

        Route::post('/settings',  'GuildController@submitSettings')->name('guild.submitSettings');

        // Can't get the permissions working right now (2019-12-02), so I'm disabling this.
        Route::get( '/permissions', 'PermissionsController@permissions')->name('guild.permissions');
        Route::get( '/addPermissions', 'PermissionsController@addPermissions')->name('guild.addPermissions');
    });
});

Route::group([
        'middleware' => 'acl',
        'is'         => env('PERMISSION_CLASS_LEADER'),
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

Route::get( '/{username}',      'ProfileController@findByUsername')->name('findUserByUsername');
