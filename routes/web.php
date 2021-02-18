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
// Route::get( 'password/reset',         'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
// Route::post('password/email',         'Auth\ForgotPasswordController@sendResetLinkEmail') ->name('password.email');
// Route::get( 'password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')       ->name('password.reset');
// Route::post('password/reset',         'Auth\ResetPasswordController@reset');
// Email Validation routes
// Route::get('email/verify',      'Auth\VerificationController@show')   ->name('verification.notice');
// Route::get('email/verify/{id}', 'Auth\VerificationController@verify') ->name('verification.verify');
// Route::get('email/resend',      'Auth\VerificationController@resend') ->name('verification.resend');
Route::group(['prefix' => 'auth'], function () {
    // Discord sign-in
    Route::get('/discord',          'Auth\LoginController@redirectToDiscord')    ->name('discordLogin');
    Route::get('/discord/callback', 'Auth\LoginController@handleDiscordCallback');
});

Route::group(['prefix' => 'loot'], function () {
    Route::get('/',                         'LootController@show')                 ->name('loot');
    Route::get('/table/{expansionId}', 'ExportController@exportExpansionLoot')->name('loot.table');
});

// Route::get( '/about',   'HomeController@about')  ->name('about');
// Route::get( '/contact', 'HomeController@contact')->name('contact');
Route::get( '/faq',     'HomeController@faq')    ->name('faq');
Route::get( '/privacy', 'HomeController@privacy')->name('privacy');
Route::get( '/terms',   'HomeController@terms')  ->name('terms');
Route::get( '/donate',  'HomeController@donate') ->name('donate');

Route::get( '/register-guild', 'GuildController@showRegister')->name('guild.showRegister');
Route::post('/submit-guild',   'GuildController@register')    ->name('guild.register');

Route::get('/streamer-mode', 'MemberController@toggleStreamerMode')->name('toggleStreamerMode');

// Route::group(['prefix' => 'item'], function () {
//     Route::get( '/{item_id}/{slug?}', 'ItemController@show')->name('item.show');
// });

Route::group([
        'middleware' => ['seeUser', 'checkGuildPermissions'],
        'prefix'     => '{guildId}/{guildSlug}'
    ], function () {

    Route::get( '/',                'GuildController@home')              ->name('guild.home');

    Route::get( '/audit-log',       'AuditLogController@index')          ->name('guild.auditLog');
    // Route::get( '/news',            'DashboardController@news')          ->name('guild.news');
    // Route::get( '/calendar',        'DashboardController@calendar')      ->name('guild.calendar');
    // Route::get( '/calendar/iframe', 'DashboardController@calendarIframe')->name('guild.calendarIframe');

    Route::group(['prefix' => 'c'], function () {
        Route::get( '/create',                        'CharacterController@showCreate')->name('character.showCreate');
        Route::post('/create',                        'CharacterController@create')    ->name('character.create');
        Route::get( '/{characterId}/{nameSlug}/edit', 'CharacterController@edit')      ->name('character.edit');
        Route::get( '/{characterId}/{nameSlug}/loot', 'CharacterController@loot')      ->name('character.loot');
        Route::post('/update',                        'CharacterController@update')    ->name('character.update');
        Route::post('/loot/update',                   'CharacterController@updateLoot')->name('character.updateLoot');
        Route::post('/note/update',                   'CharacterController@updateNote')->name('character.updateNote');
        Route::get( '/{characterId}/{nameSlug}',      'CharacterController@show')      ->name('character.show');
        Route::get( '/{nameSlug}',                    'CharacterController@find')      ->name('character.find');
    });

    Route::get( '/loot/{instanceSlug}',      'ItemController@listWithGuild')      ->name('guild.item.list');
    Route::get( '/loot/{instanceSlug}/edit', 'ItemController@listWithGuildEdit')  ->name('guild.item.list.edit');
    Route::post('/loot/{instanceSlug}/edit', 'ItemController@listWithGuildSubmit')->name('guild.item.list.submit');

    Route::group(['prefix' => 'i'], function () {
        Route::get( '/{item_id}/{slug?}',        'ItemController@showWithGuild')    ->name('guild.item.show');
        Route::post('/note/update',              'ItemController@updateNote')       ->name('guild.item.updateNote');

        Route::get( '/{item_id}/{raidId}/prios', 'PrioController@singleInput')      ->name('guild.item.prios');
        Route::post('/prios',                    'PrioController@submitSingleInput')->name('guild.item.prios.submit');
    });

    Route::group(['prefix' => 'members'], function () {
        Route::get( '/', 'MemberController@showList')->name('guild.members.list');
    });

    Route::group(['prefix' => 'u'], function () {
        Route::get( '/{memberId}/{usernameSlug}/edit', 'MemberController@edit')      ->name('member.edit');
        Route::post('/update',                         'MemberController@update')    ->name('member.update');
        Route::post('/note/update',                    'MemberController@updateNote')->name('member.updateNote');
        Route::get( '/{memberId}/{usernameSlug}',      'MemberController@show')      ->name('member.show');
        Route::get( '/{usernameSlug}',                 'MemberController@find')      ->name('member.find');
    });

    // Route::get( '/resources',        'ContentController@index')->name('contentIndex');
    // Route::get( '/resources/{slug}', 'ContentController@show') ->name('showContent');
    // Route::get( '/posts/{slug}',     'ContentController@show') ->name('showPost');

    Route::get( '/roster',          'DashboardController@roster')->name('guild.roster');

    Route::get( '/assign-loot', 'ItemController@massInput')      ->name('item.massInput');
    Route::post('/assign-loot', 'ItemController@submitMassInput')->name('item.massInput.submit');


    Route::group(['prefix' => 'raid'], function () {
        Route::get( '/',               'RaidController@raids')        ->name('guild.raids');
        Route::get( '/create',         'RaidController@edit')         ->name('guild.raid.create');
        Route::get( '/edit/{id?}',     'RaidController@edit')         ->name('guild.raid.edit');
        Route::post('/toggle-disable', 'RaidController@toggleDisable')->name('guild.raid.toggleDisable');
        Route::post('/update',         'RaidController@update')       ->name('guild.raid.update');
        Route::post('/',               'RaidController@create')       ->name('guild.raid.create');

        Route::group(['prefix' => 'prio'], function () {
            Route::get( '/{instanceSlug}',          'PrioController@chooseRaid')     ->name('guild.prios.chooseRaid');
            Route::get( '/{instanceSlug}/{raidId}', 'PrioController@massInput')      ->name('guild.prios.massInput');
            Route::post('/',                        'PrioController@submitMassInput')->name('guild.prios.massInput.submit');
        });
    });

    Route::get( '/register-expansion/{expansionSlug}', 'GuildController@showRegisterExpansion')->name('guild.showRegisterExpansion');
    Route::post('/register-expansion/{expansionSlug}', 'GuildController@registerExpansion')    ->name('guild.registerExpansion');

    Route::get( '/roles',     'RoleController@roles')    ->name('guild.roles');
    Route::get( '/syncRoles', 'RoleController@syncRoles')->name('guild.syncRoles');

    Route::get( '/settings',  'GuildController@settings')->name('guild.settings');
    Route::post('/settings',  'GuildController@submitSettings')->name('guild.submitSettings');

    Route::get( '/change-owner',  'GuildController@owner')      ->name('guild.owner');
    Route::post('/change-owner',  'GuildController@submitOwner')->name('guild.submitOwner');

    // Can't get the permissions working right now (2019-12-02), so I'm disabling this.
    Route::get( '/permissions', 'PermissionsController@permissions')->name('guild.permissions');
    Route::get( '/addPermissions', 'PermissionsController@addPermissions')->name('guild.addPermissions');

    Route::group(['prefix' => 'export'], function () {
        Route::get('/',                      'GuildController@showExports')               ->name('guild.exports');
        Route::get('/characters-with-items', 'ExportController@exportCharactersWithItems')->name('guild.export.charactersWithItems');
        Route::get('/item-notes',            'ExportController@exportItemNotes')          ->name('guild.export.itemNotes');
        Route::get('/loot',                  'ExportController@exportLoot')               ->name('guild.export.loot');
        Route::get('/prios',                 'ExportController@exportPrios')              ->name('guild.export.prio');
        Route::get('/wishlist',              'ExportController@exportWishlists')          ->name('guild.export.wishlist');
    });
});

Route::get('/{guildSlug}', 'GuildController@find')->name('guild.find');

// Route::group([
//         'middleware' => 'acl',
//         'is'         => env('PERMISSION_CLASS_LEADER'),
//     ], function () {
//     Route::post('/updateContent/{id?}', 'ContentController@update')->where('id', '[0-9]+')->name('updateContent');
//     Route::post('/removeContent/{id}',  'ContentController@remove')->where('id', '[0-9]+')->name('removeContent');
// });
