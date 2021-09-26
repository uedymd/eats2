<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Rakuten\RakutenItemController;
use App\Http\Controllers\EbayItemController;
use App\Http\Controllers\StocksController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::group(['prefix' => 'rakuten'], function () {
    Route::get('search/{id?}', [RakutenItemController::class, 'search'])->name('api.rakuten.search')->where('id', '[0-9]+');
    Route::get('translate/get_url', [RakutenItemController::class, 'get_url']);
    Route::get('translate/get_title', [RakutenItemController::class, 'get_title']);
    Route::get('translate/get_brand', [RakutenItemController::class, 'get_brand']);
    Route::get('translate/get_content', [RakutenItemController::class, 'get_content']);
    Route::get('translate/get_price', [RakutenItemController::class, 'get_price']);
    Route::get('translate/get_image', [RakutenItemController::class, 'get_image']);
    Route::post('translate/set_content', [RakutenItemController::class, 'set_content']);
    Route::get('translate/set_content', [RakutenItemController::class, 'set_content']);
    Route::post('translate/set_doller', [RakutenItemController::class, 'set_doller']);
    Route::get('translate/set_doller', [RakutenItemController::class, 'set_doller']);
    Route::post('translate/set_title', [RakutenItemController::class, 'set_title']);
    Route::post('translate/set_brand', [RakutenItemController::class, 'set_brand']);
    Route::post('translate/set_en_content', [RakutenItemController::class, 'set_en_content']);
});

Route::group(['prefix' => 'ebay'], function () {
    Route::get('add/item/{site}/{id?}/', [EbayItemController::class, 'add']);
    Route::get('add/items/', [EbayItemController::class, 'add_items']);
    Route::get('set/items_detail/', [EbayItemController::class, 'set_items_detail']);
});

Route::group(['prefix' => 'stock', 'middleware' => ['auth']], function () {
    Route::get('/search', [StocksController::class, 'search'])->name('stock.seach');
});
