<?php

namespace App\Http\Controllers;

use Route;

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

Route::get('/', [HomeController::class, 'index'])->name('home');

// Registration routes:
Route::get('/register', [Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [Auth\RegisterController::class, 'register']);

// Authentication routes:
Route::get('/login', [Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [Auth\LoginController::class, 'login']);
Route::get('/logout', [Auth\LoginController::class, 'logout']);
Route::post('/logout', [Auth\LoginController::class, 'logout'])->name('logout');

// Discord sign-in
Route::prefix('auth')->group(function () {
    Route::get('/discord', [Auth\LoginController::class, 'redirectToDiscord'])->name('discordLogin');
    Route::get('/discord/callback', [Auth\LoginController::class, 'handleDiscordCallback']);
});

Route::prefix('loot')->middleware('seeUser')->group(function () {
    Route::get('/', [LootController::class, 'show'])->name('loot');
    Route::get('/list/{expansionId}/{instanceSlug}', [LootController::class, 'list'])->name('loot.list');
    Route::get('/table/{expansionSlug}/{type}', [ExportController::class, 'exportExpansionLoot'])->name('loot.table');
    Route::get('/wishlists/{expansionId}', [LootController::class, 'showWishlistStats'])->name('loot.wishlist');
});

Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/donate', [HomeController::class, 'donate'])->name('donate');
Route::get('/translations', [HomeController::class, 'translations'])->name('translations');

Route::get('/register-guild', [GuildController::class, 'showRegister'])->name('guild.showRegister');
Route::post('/submit-guild', [GuildController::class, 'register'])->name('guild.register');

Route::get('/streamer-mode', [MemberController::class, 'toggleStreamerMode'])->name('toggleStreamerMode');
Route::post('/set-locale', [MemberController::class, 'setLocale'])->name('setLocale');

Route::get('/admin/guilds', [AdminController::class, 'showGuilds'])->name('admin.guilds')->middleware(['seeUser', 'checkAdmin']);

Route::get('/{guildSlug}', [GuildController::class, 'find'])->name('guild.find');