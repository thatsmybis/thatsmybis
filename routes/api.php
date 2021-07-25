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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/items/query/{expansion_id}/{query}/{locale?}', 'Api\ItemController@query')->where('expansion_id', '[0-9]+')->name('apiSearchItems');

Route::get('/raids/query/{guild_id}/{query}', 'Api\RaidController@query')->where('guild_id', '[0-9]+')->name('apiSearchRaids');

Route::get('/batches/query/{guild_id}/{query}', 'Api\BatchController@query')->where('guild_id', '[0-9]+')->name('apiSearchBatches');
