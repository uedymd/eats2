<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Rakuten\RakutenItemController;
use App\Http\Controllers\EbayItemController;
use App\Http\Controllers\StocksController;
use App\Http\Controllers\DigimartItemsController;
use App\Http\Controllers\ExcludeItemsController;
use App\Http\Controllers\HardoffItemsController;
use App\Http\Controllers\SecoundstreetItemsController;
use App\Http\Controllers\KurosawaItemController;
use App\Http\Controllers\MikigakkiItemController;
use App\Http\Controllers\MessageController;

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
Route::group(['prefix' => 'digimart'], function () {
    Route::get('search/{id?}', [DigimartItemsController::class, 'search'])->name('api.digimart.search')->where('id', '[0-9]+');
    Route::get('translate/get_url', [DigimartItemsController::class, 'get_url']);
    Route::get('translate/get_title', [DigimartItemsController::class, 'get_title']);
    Route::get('translate/get_brand', [DigimartItemsController::class, 'get_brand']);
    Route::get('translate/get_content', [DigimartItemsController::class, 'get_content']);
    Route::get('translate/get_price', [DigimartItemsController::class, 'get_price']);
    Route::get('translate/get_image', [DigimartItemsController::class, 'get_image']);
    Route::post('translate/set_content', [DigimartItemsController::class, 'set_content']);
    Route::post('translate/delete_content', [DigimartItemsController::class, 'delete_content']);
    Route::get('translate/set_content', [DigimartItemsController::class, 'set_content']);
    Route::get('translate/delete_content', [DigimartItemsController::class, 'delete_content']);
    Route::post('translate/set_doller', [DigimartItemsController::class, 'set_doller']);
    Route::get('translate/set_doller', [DigimartItemsController::class, 'set_doller']);
    Route::post('translate/set_title', [DigimartItemsController::class, 'set_title']);
    Route::post('translate/set_brand', [DigimartItemsController::class, 'set_brand']);
    Route::post('translate/set_en_content', [DigimartItemsController::class, 'set_en_content']);
});
Route::group(['prefix' => 'hardoff'], function () {
    Route::get('search/{id?}', [HardoffItemsController::class, 'search'])->name('api.hardoff.search')->where('id', '[0-9]+');
    Route::get('translate/get_url', [HardoffItemsController::class, 'get_url']);
    Route::get('translate/get_title', [HardoffItemsController::class, 'get_title']);
    Route::get('translate/get_brand', [HardoffItemsController::class, 'get_brand']);
    Route::get('translate/get_content', [HardoffItemsController::class, 'get_content']);
    Route::get('translate/get_price', [HardoffItemsController::class, 'get_price']);
    Route::get('translate/get_image', [HardoffItemsController::class, 'get_image']);
    Route::post('translate/set_content', [HardoffItemsController::class, 'set_content']);
    Route::post('translate/delete_content', [HardoffItemsController::class, 'delete_content']);
    Route::get('translate/set_content', [HardoffItemsController::class, 'set_content']);
    Route::get('translate/delete_content', [HardoffItemsController::class, 'delete_content']);
    Route::post('translate/set_doller', [HardoffItemsController::class, 'set_doller']);
    Route::get('translate/set_doller', [HardoffItemsController::class, 'set_doller']);
    Route::post('translate/set_title', [HardoffItemsController::class, 'set_title']);
    Route::post('translate/set_brand', [HardoffItemsController::class, 'set_brand']);
    Route::post('translate/set_en_content', [HardoffItemsController::class, 'set_en_content']);
});

Route::group(['prefix' => 'secoundstreet'], function () {
    Route::get('search/{id?}', [SecoundstreetItemsController::class, 'search'])->name('api.secoundstreet.search')->where('id', '[0-9]+');
    Route::get('translate/get_url', [SecoundstreetItemsController::class, 'get_url']);
    Route::get('translate/get_title', [SecoundstreetItemsController::class, 'get_title']);
    Route::get('translate/get_brand', [SecoundstreetItemsController::class, 'get_brand']);
    Route::get('translate/get_content', [SecoundstreetItemsController::class, 'get_content']);
    Route::get('translate/get_price', [SecoundstreetItemsController::class, 'get_price']);
    Route::get('translate/get_image', [SecoundstreetItemsController::class, 'get_image']);
    Route::post('translate/set_content', [SecoundstreetItemsController::class, 'set_content']);
    Route::post('translate/delete_content', [SecoundstreetItemsController::class, 'delete_content']);
    Route::get('translate/set_content', [SecoundstreetItemsController::class, 'set_content']);
    Route::get('translate/delete_content', [SecoundstreetItemsController::class, 'delete_content']);
    Route::post('translate/set_doller', [SecoundstreetItemsController::class, 'set_doller']);
    Route::get('translate/set_doller', [SecoundstreetItemsController::class, 'set_doller']);
    Route::post('translate/set_title', [SecoundstreetItemsController::class, 'set_title']);
    Route::post('translate/set_brand', [SecoundstreetItemsController::class, 'set_brand']);
    Route::post('translate/set_en_content', [SecoundstreetItemsController::class, 'set_en_content']);
});

Route::group(['prefix' => 'kurosawa'], function () {
    Route::get('search/{id?}', [KurosawaItemController::class, 'search'])->name('api.kurosawa.search')->where('id', '[0-9]+');
    Route::get('translate/get_url', [KurosawaItemController::class, 'get_url']);
    Route::get('translate/get_title', [KurosawaItemController::class, 'get_title']);
    Route::get('translate/get_brand', [KurosawaItemController::class, 'get_brand']);
    Route::get('translate/get_content', [KurosawaItemController::class, 'get_content']);
    Route::get('translate/get_price', [KurosawaItemController::class, 'get_price']);
    Route::get('translate/get_image', [KurosawaItemController::class, 'get_image']);
    Route::post('translate/set_content', [KurosawaItemController::class, 'set_content']);
    Route::post('translate/delete_content', [KurosawaItemController::class, 'delete_content']);
    Route::get('translate/set_content', [KurosawaItemController::class, 'set_content']);
    Route::get('translate/delete_content', [KurosawaItemController::class, 'delete_content']);
    Route::post('translate/set_doller', [KurosawaItemController::class, 'set_doller']);
    Route::get('translate/set_doller', [KurosawaItemController::class, 'set_doller']);
    Route::post('translate/set_title', [KurosawaItemController::class, 'set_title']);
    Route::post('translate/set_brand', [KurosawaItemController::class, 'set_brand']);
    Route::post('translate/set_en_content', [KurosawaItemController::class, 'set_en_content']);
});

Route::group(['prefix' => 'mikigakki'], function () {
    Route::get('search/{id?}', [MikigakkiItemController::class, 'search'])->name('api.mikigakki.search')->where('id', '[0-9]+');
    Route::get('translate/get_url', [MikigakkiItemController::class, 'get_url']);
    Route::get('translate/get_title', [MikigakkiItemController::class, 'get_title']);
    Route::get('translate/get_brand', [MikigakkiItemController::class, 'get_brand']);
    Route::get('translate/get_content', [MikigakkiItemController::class, 'get_content']);
    Route::get('translate/get_price', [MikigakkiItemController::class, 'get_price']);
    Route::get('translate/get_image', [MikigakkiItemController::class, 'get_image']);
    Route::post('translate/set_content', [MikigakkiItemController::class, 'set_content']);
    Route::post('translate/delete_content', [MikigakkiItemController::class, 'delete_content']);
    Route::get('translate/set_content', [MikigakkiItemController::class, 'set_content']);
    Route::get('translate/delete_content', [MikigakkiItemController::class, 'delete_content']);
    Route::post('translate/set_doller', [MikigakkiItemController::class, 'set_doller']);
    Route::get('translate/set_doller', [MikigakkiItemController::class, 'set_doller']);
    Route::post('translate/set_title', [MikigakkiItemController::class, 'set_title']);
    Route::post('translate/set_brand', [MikigakkiItemController::class, 'set_brand']);
    Route::post('translate/set_en_content', [MikigakkiItemController::class, 'set_en_content']);
});

Route::group(['prefix' => 'ebay'], function () {
    Route::get('add/item/{site}/{id?}/', [EbayItemController::class, 'add']);
    Route::get('add/items/', [EbayItemController::class, 'add_items']);
    Route::get('set/items_detail/', [EbayItemController::class, 'set_items_detail']);
    Route::get('tracking/{site}', [EbayItemController::class, 'tracking']);
    Route::post('set/tracking/{site}', [EbayItemController::class, 'set_tracking']);
});

Route::group(['prefix' => 'stock'], function () {
    Route::get('/search/{site}', [StocksController::class, 'search']);
});

Route::group(['prefix' => 'exclude_items'], function () {
    Route::get('/exclude', [ExcludeItemsController::class, 'exclude']);
});

Route::group(['prefix' => 'message'], function () {
    Route::get('/', [MessageController::class, 'get_messages']);
    Route::post('/side_items', [MessageController::class, 'get_side_items']);
    Route::post('/item_detail/{id}', [MessageController::class, 'get_item_detail'])->where('id', '[0-9]+');
});
