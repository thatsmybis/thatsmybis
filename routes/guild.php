<?php

namespace App\Http\Controllers;

use Route;

/*
|--------------------------------------------------------------------------
| Guild Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application that are
| prefixed with a guild's ID and slug (https://domain.tld/guildId/guildSlug)
|
*/

Route::prefix('{guildId}/{guildSlug}')->middleware(['seeUser', 'checkGuildPermissions'])->group(function () {
    Route::get('/', [GuildController::class, 'home'])->name('guild.home');
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('guild.auditLog');

    Route::group(['prefix' => 'c'], function () {
        Route::get('/create', [CharacterController::class, 'showCreate'])->name('character.showCreate');
        Route::post('/create', [CharacterController::class, 'create'])->name('character.create');
        Route::get('/{characterId}/{characterSlug}/edit', [CharacterController::class, 'edit'])->name('character.edit');
        Route::post('/update', [CharacterController::class, 'update'])->name('character.update');
        Route::post('/note/update', [CharacterController::class, 'updateNote'])->name('character.updateNote');
        Route::get('/{characterId}/{characterSlug}', [CharacterController::class, 'show'])->name('character.show');
        Route::get('/{characterSlug}', [CharacterController::class, 'find'])->name('character.find');
        Route::get('/{characterId}/{characterSlug}/loot', [CharacterLootController::class, 'loot'])->name('character.loot');
        Route::post('/loot/update', [CharacterLootController::class, 'updateLoot'])->name('character.updateLoot');
    });

    Route::get('/gquit', [MemberController::class, 'showGquit'])->name('member.showGquit');
    Route::post('/gquit', [MemberController::class, 'submitGquit'])->name('member.submitGquit');

    Route::get('/loot/recipes', [RecipeController::class, 'listRecipesWithGuild'])->name('guild.recipe.list');
    Route::get('/loot/wishlists', [LootController::class, 'showWishlistStatsInGuild'])->name('guild.loot.wishlist');

    Route::get('/loot/{instanceSlug}', [ItemController::class, 'listWithGuild'])->name('guild.item.list');
    Route::get('/loot/{instanceSlug}/edit', [ItemNoteController::class, 'listWithGuildEdit'])->name('guild.item.list.edit');
    Route::post('/loot/{instanceSlug}/edit', [ItemNoteController::class, 'listWithGuildSubmit'])->name('guild.item.list.submit');

    Route::prefix('i')->group(function () {
        Route::get('/{itemId}/{itemSlug?}', [ItemController::class, 'showWithGuild'])->name('guild.item.show');
        Route::post('/note/update', [ItemNoteController::class, 'updateNote'])->name('guild.item.updateNote');

        Route::get('/{itemId}/{raidGroupId}/prios', [PrioController::class, 'singleInput'])->name('guild.item.prios');
        Route::post('/prios', [PrioController::class, 'submitSingleInput'])->name('guild.item.prios.submit');
    });

    Route::prefix('members')->group(function () {
        Route::get('/', [MemberController::class, 'showList'])->name('guild.members.list');
    });

    Route::prefix('u')->group(function () {
        Route::get('/{memberId}/{userSlug}', [MemberController::class, 'show'])->name('member.show');
        Route::get('/{memberId}/{userSlug}/edit', [MemberController::class, 'edit'])->name('member.edit');
        Route::post('/update', [MemberController::class, 'update'])->name('member.update');
        Route::post('/note/update', [MemberController::class, 'updateNote'])->name('member.updateNote');
        Route::get('/{userSlug}', [MemberController::class, 'find'])->name('member.find');
        Route::post('/set-raid-group-filter', [MemberController::class, 'setRaidGroupFilter'])->name('setRaidGroupFilter');
    });

    Route::get('/roster', [RosterController::class, 'roster'])->name('guild.roster');

    Route::get('/assign-loot', [AssignLootController::class, 'assignLoot'])->name('item.assignLoot');
    Route::post('/assign-loot', [AssignLootController::class, 'submitAssignLoot'])->name('item.assignLoot.submit');
    Route::get('/assign-loot/edit/{batchId}', [AssignLootController::class, 'assignLootShowEdit'])->name('item.assignLoot.edit');
    Route::post('/assign-loot/edit', [AssignLootController::class, 'assignLootSubmitEdit'])->name('item.assignLoot.submitEdit');
    Route::get('/assign-loot/list', [AssignLootController::class, 'listAssignedLoot'])->name('item.assignLoot.list');

    Route::prefix('raids')->group(function () {
        Route::get('/', [RaidController::class, 'list'])->name('guild.raids.list');
        Route::get('/new', [RaidController::class, 'showEdit'])->name('guild.raids.new');
        Route::get('/copy/{raidId}', [RaidController::class, 'copy'])->name('guild.raids.copy');
        Route::get('/edit/{raidId}', [RaidController::class, 'showEdit'])->name('guild.raids.edit');
        Route::post('/update', [RaidController::class, 'update'])->name('guild.raids.update');
        Route::post('/new', [RaidController::class, 'create'])->name('guild.raids.create');
        Route::get('/{raidId}/{raidSlug?}', [RaidController::class, 'show'])->name('guild.raids.show');
    });

    Route::prefix('raid-groups')->group(function () {
        Route::get('/', [RaidGroupController::class, 'raidGroups'])->name('guild.raidGroups');
        Route::get('/create', [RaidGroupController::class, 'edit'])->name('guild.raidGroup.create');
        Route::get('/edit/{id?}', [RaidGroupController::class, 'edit'])->name('guild.raidGroup.edit');
        Route::get('/{id}/attendance', [RaidGroupController::class, 'attendance'])->name('guild.raidGroup.attendance');
        Route::get('/{id}/characters/main', [RaidGroupController::class, 'mainCharacters'])->name('guild.raidGroup.mainCharacters');
        Route::get('/{id}/characters/general', [RaidGroupController::class, 'secondaryCharacters'])->name('guild.raidGroup.secondaryCharacters');
        Route::post('/toggle-disable', [RaidGroupController::class, 'toggleDisable'])->name('guild.raidGroup.toggleDisable');
        Route::post('/update', [RaidGroupController::class, 'update'])->name('guild.raidGroup.update');
        Route::post('/update-characters', [RaidGroupController::class, 'updateMainCharacters'])->name('guild.raidGroup.updateMainCharacters');
        Route::post('/update-other-characters', [RaidGroupController::class, 'updateSecondaryCharacters'])->name('guild.raidGroup.updateSecondaryCharacters');
        Route::post('/', [RaidGroupController::class, 'create'])->name('guild.raidGroup.create'); // TODO: This or the copy a few lines up needs to go, wuth some testing

        Route::prefix('prio')->group(function () {
            Route::get('/{instanceSlug}', [PrioController::class, 'chooseRaidGroup'])->name('guild.prios.chooseRaidGroup');
            Route::get('/{instanceSlug}/{id}', [PrioController::class, 'assignPrios'])->name('guild.prios.assignPrios');
            Route::post('/', [PrioController::class, 'submitAssignPrios'])->name('guild.prios.assignPrios.submit');
        });
    });

    Route::get('/register-expansion/{expansionSlug}', [GuildController::class, 'showRegisterExpansion'])->name('guild.showRegisterExpansion');
    Route::post('/register-expansion/{expansionSlug}', [GuildController::class, 'registerExpansion'])->name('guild.registerExpansion');

    Route::get('/roles', [RoleController::class, 'roles'])->name('guild.roles');
    Route::get('/syncRoles', [RoleController::class, 'syncRoles'])->name('guild.syncRoles');

    Route::get('/settings', [GuildController::class, 'settings'])->name('guild.settings');
    Route::post('/settings', [GuildController::class, 'submitSettings'])->name('guild.submitSettings');

    Route::get('/change-owner', [GuildController::class, 'owner'])->name('guild.owner');
    Route::post('/change-owner', [GuildController::class, 'submitOwner'])->name('guild.submitOwner');

    Route::get('/change-discord', [GuildController::class, 'changeDiscord'])->name('guild.changeDiscord');
    Route::post('/change-discord', [GuildController::class, 'submitChangeDiscord'])->name('guild.submitChangeDiscord');

    // Can't get the permissions working right now (2019-12-02), so I'm disabling this.
    Route::get('/permissions', [PermissionsController::class, 'permissions'])->name('guild.permissions');
    Route::get('/addPermissions', [PermissionsController::class, 'addPermissions'])->name('guild.addPermissions');

    Route::prefix('export')->group(function () {
        Route::get('/', [GuildController::class, 'showExports'])->name('guild.exports');
        Route::get('/addon/{fileType}', [ExportController::class, 'exportAddonItems'])->name('guild.export.addonItems')->where(['fileType' => '(csv|html)']);
        Route::get('/characters-with-items/{fileType}', [ExportController::class, 'exportCharactersWithItems'])->name('guild.export.charactersWithItems')->where(['fileType' => '(html|json)']);
        Route::get('/gargul', [ExportController::class, 'gargulWishlistJson'])->name('guild.export.gargul');
        Route::get('/item-notes/{fileType}', [ExportController::class, 'exportItemNotes'])->name('guild.export.itemNotes')->where(['fileType' => '(csv|html)']);
        Route::get('/loot/{fileType}/{lootType}', [ExportController::class, 'exportGuildLoot'])->name('guild.export.loot')->where(['lootType' => '(all|prio|received|wishlist)']);
        Route::get('/raid-groups/{fileType}/{id?}', [ExportController::class, 'exportRaidGroups'])->name('guild.export.raidGroups')->where(['fileType' => '(csv|html)']);
    });
});