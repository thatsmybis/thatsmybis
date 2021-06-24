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
Route::get( 'logout', 'Auth\LoginController@logout');
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
    Route::get('/',                                      'LootController@show')                 ->name('loot');
    Route::get('/list/{expansionId}/{instanceSlug}',     'LootController@list')                 ->name('loot.list');
    Route::get('/table/{expansionSlug}/{type}',          'ExportController@exportExpansionLoot')->name('loot.table');
    Route::get('/wishlists/{expansionId}',               'LootController@showWishlistStats')    ->name('loot.wishlist');
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
        'middleware' => ['seeUser', 'checkAdmin'],
        'prefix'     => 'admin'
    ], function () {
    Route::get( '/guilds', 'AdminController@showGuilds')->name('admin.guilds');
});

Route::group([
        'middleware' => ['seeUser', 'checkGuildPermissions'],
        'prefix'     => '{guildId}/{guildSlug}',
        'where'      => ['guildId' => '[0-9]+'],
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
        Route::post('/update',                        'CharacterController@update')    ->name('character.update');
        Route::post('/note/update',                   'CharacterController@updateNote')->name('character.updateNote');
        Route::get( '/{characterId}/{nameSlug}',      'CharacterController@show')      ->name('character.show');
        Route::get( '/{nameSlug}',                    'CharacterController@find')      ->name('character.find');
        Route::get( '/{characterId}/{nameSlug}/loot', 'CharacterLootController@loot')      ->name('character.loot');
        Route::post('/loot/update',                   'CharacterLootController@updateLoot')->name('character.updateLoot');
    });

    Route::get( '/gquit', 'MemberController@showGquit')  ->name('member.showGquit');
    Route::post('/gquit', 'MemberController@submitGquit')->name('member.submitGquit');

    Route::get( '/loot/recipes',   'RecipeController@listRecipesWithGuild')  ->name('guild.recipe.list');
    Route::get( '/loot/wishlists', 'LootController@showWishlistStatsInGuild')->name('guild.loot.wishlist');

    Route::get( '/loot/{instanceSlug}',      'ItemController@listWithGuild')           ->name('guild.item.list');
    Route::get( '/loot/{instanceSlug}/edit', 'ItemNoteController@listWithGuildEdit')   ->name('guild.item.list.edit');
    Route::post('/loot/{instanceSlug}/edit', 'ItemNoteController@listWithGuildSubmit') ->name('guild.item.list.submit');

    Route::group(['prefix' => 'i'], function () {
        Route::get( '/{item_id}/{slug?}', 'ItemController@showWithGuild') ->name('guild.item.show');
        Route::post('/note/update',       'ItemNoteController@updateNote')->name('guild.item.updateNote');

        Route::get( '/{item_id}/{raidGroupId}/prios', 'PrioController@singleInput')      ->name('guild.item.prios');
        Route::post('/prios',                         'PrioController@submitSingleInput')->name('guild.item.prios.submit');
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

    Route::get( '/roster',          'RosterController@roster')->name('guild.roster');

    Route::get( '/assign-loot', 'AssignLootController@massInput')      ->name('item.massInput');
    Route::post('/assign-loot', 'AssignLootController@submitMassInput')->name('item.massInput.submit');

    Route::group(['prefix' => 'raids'], function () {
        Route::get( '/',                'RaidController@list')    ->name('guild.raids.list');
        Route::get( '/new',             'RaidController@showEdit')->name('guild.raids.new');
        Route::get( '/copy/{raidId}',   'RaidController@copy',   )->name('guild.raids.copy');
        Route::get( '/edit/{raidId}',   'RaidController@showEdit')->where('id', '[0-9]+')->name('guild.raids.edit');
        Route::post('/update',          'RaidController@update')  ->name('guild.raids.update');
        Route::post('/new',             'RaidController@create')  ->name('guild.raids.create');
        Route::get( '/{raidId}/{raidSlug?}', 'RaidController@show')->where('id', '[0-9]+')->name('guild.raids.show');
    });

    Route::group(['prefix' => 'raid-groups'], function () {
        Route::get( '/',                        'RaidGroupController@raidGroups')               ->name('guild.raidGroups');
        Route::get( '/create',                  'RaidGroupController@edit')                     ->name('guild.raidGroup.create');
        Route::get( '/edit/{id?}',              'RaidGroupController@edit')                     ->where('id', '[0-9]+')->name('guild.raidGroup.edit');
        Route::get( '/{id}/characters/main',    'RaidGroupController@mainCharacters')               ->where('id', '[0-9]+')->name('guild.raidGroup.mainCharacters');
        Route::get( '/{id}/characters/general', 'RaidGroupController@secondaryCharacters')      ->where('id', '[0-9]+')->name('guild.raidGroup.secondaryCharacters');
        Route::post('/toggle-disable',          'RaidGroupController@toggleDisable')            ->name('guild.raidGroup.toggleDisable');
        Route::post('/update',                  'RaidGroupController@update')                   ->name('guild.raidGroup.update');
        Route::post('/update-characters',       'RaidGroupController@updateMainCharacters')     ->name('guild.raidGroup.updateMainCharacters');
        Route::post('/update-other-characters', 'RaidGroupController@updateSecondaryCharacters')->name('guild.raidGroup.updateSecondaryCharacters');
        Route::post('/',                        'RaidGroupController@create')                   ->name('guild.raidGroup.create'); // TODO: This or the copy a few lines up needs to go, wuth some testing

        Route::group(['prefix' => 'prio'], function () {
            Route::get( '/{instanceSlug}',               'PrioController@chooseRaidGroup')->name('guild.prios.chooseRaidGroup');
            Route::get( '/{instanceSlug}/{raidGroupId}', 'PrioController@massInput')      ->where('raidGroupId', '[0-9]+')->name('guild.prios.massInput');
            Route::post('/',                             'PrioController@submitMassInput')->name('guild.prios.massInput.submit');
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

    Route::get( '/change-discord', 'GuildController@changeDiscord')      ->name('guild.changeDiscord');
    Route::post('/change-discord', 'GuildController@submitChangeDiscord')->name('guild.submitChangeDiscord');

    // Can't get the permissions working right now (2019-12-02), so I'm disabling this.
    Route::get( '/permissions', 'PermissionsController@permissions')->name('guild.permissions');
    Route::get( '/addPermissions', 'PermissionsController@addPermissions')->name('guild.addPermissions');

    Route::group(['prefix' => 'export'], function () {
        Route::get('/',                                      'GuildController@showExports')               ->name('guild.exports');
        Route::get('/addon/{fileType}',                      'ExportController@exportAddonItems')         ->name('guild.export.addonItems')         ->where(['fileType' => '(html|json)']);
        Route::get('/characters-with-items/{fileType}',      'ExportController@exportCharactersWithItems')->name('guild.export.charactersWithItems')->where(['fileType' => '(html|json)']);
        Route::get('/item-notes/{fileType}',                 'ExportController@exportItemNotes')          ->name('guild.export.itemNotes')          ->where(['fileType' => '(csv|html)']);
        Route::get('/loot/{fileType}/{lootType}',            'ExportController@exportGuildLoot')          ->name('guild.export.loot')               ->where(['fileType' => '(csv|html)', 'lootType' => '(all|prio|received|wishlist)']);
        Route::get('/raid-groups/{fileType}/{raidGroupId?}', 'ExportController@exportRaidGroups')         ->name('guild.export.raidGroups')         ->where(['fileType' => '(csv|html)']);
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
