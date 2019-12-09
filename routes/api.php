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

Route::post('/officer-note/{id}', [
        'uses'       => 'Api\ProfileController@submitOfficerNote',
        'middleware' => 'acl',
        'is'         => env('PERMISSION_RAID_LEADER'),
    ])->name('apiOfficerNote');

Route::get( '/items/query/{query}', 'Api\ItemsController@query')->name('apiSearchItems');
