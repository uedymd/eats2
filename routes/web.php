<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Rakuten\RakutenController;
use App\Http\Controllers\Rakuten\RakutenItemController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\EbayItemController;
use App\Http\Controllers\BrandSetController;
use App\Http\Controllers\RateSetController;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\StocksController;
use App\Http\Controllers\DigimartsController;
use App\Http\Controllers\DigimartItemsController;

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

Route::redirect('/', '/login');
Route::redirect('/dashboard', '/ebay/trading')->middleware(['auth'])->name('dashboard');

Route::group(['prefix' => 'setting', 'middleware' => ['auth']], function () {
    Route::get('edit/{site}', [SettingController::class, 'edit'])->name('setting.edit');
    Route::post('update/{site}', [SettingController::class, 'update'])->name('setting.update');
    Route::group(['prefix' => 'brandset'], function () {
        Route::get('/', [BrandSetController::class, 'index'])->name('setting.brandset.index');
        Route::get('/create', [BrandSetController::class, 'create'])->name('setting.brandset.create');
        Route::post('/store', [BrandSetController::class, 'store'])->name('setting.brandset.store');
        Route::get('/edit/{id}', [BrandSetController::class, 'edit'])->name('setting.brandset.edit')->where('id', '[0-9]+');
        Route::post('/update/{id}', [BrandSetController::class, 'update'])->name('setting.brandset.update')->where('id', '[0-9]+');
        Route::get('/destroy/{id}', [BrandSetController::class, 'destroy'])->name('setting.brandset.destroy')->where('id', '[0-9]+');
    });
    Route::group(['prefix' => 'rateset'], function () {
        Route::get('/', [RateSetController::class, 'index'])->name('setting.rateset.index');
        Route::get('/create', [RateSetController::class, 'create'])->name('setting.rateset.create');
        Route::post('/store', [RateSetController::class, 'store'])->name('setting.rateset.store');
        Route::get('/edit/{id}', [RateSetController::class, 'edit'])->name('setting.rateset.edit')->where('id', '[0-9]+');
        Route::post('/update/{id}', [RateSetController::class, 'update'])->name('setting.rateset.update')->where('id', '[0-9]+');
        Route::get('/destroy/{id}', [RateSetController::class, 'destroy'])->name('setting.rateset.destroy')->where('id', '[0-9]+');
    });
    Route::group(['prefix' => 'template'], function () {
        Route::get('/', [TemplatesController::class, 'index'])->name('setting.template.index');
        Route::get('/create', [TemplatesController::class, 'create'])->name('setting.template.create');
        Route::post('/store', [TemplatesController::class, 'store'])->name('setting.template.store');
        Route::get('/edit/{id}', [TemplatesController::class, 'edit'])->name('setting.template.edit')->where('id', '[0-9]+');
        Route::post('/update/{id}', [TemplatesController::class, 'update'])->name('setting.template.update')->where('id', '[0-9]+');
        Route::get('/destroy/{id}', [TemplatesController::class, 'destroy'])->name('setting.template.destroy')->where('id', '[0-9]+');
    });
});

Route::group(['prefix' => 'rakuten', 'middleware' => ['auth']], function () {
    Route::get('/', [RakutenController::class, 'index'])->name('rakuten.index');
    Route::group(['prefix' => 'reserve'], function () {
        Route::get('create', [RakutenController::class, 'create'])->name('rakuten.create');
        Route::post('store', [RakutenController::class, 'store'])->name('rakuten.store');
        Route::get('edit/{id}', [RakutenController::class, 'edit'])->name('rakuten.edit')->where('id', '[0-9]+');
        Route::post('update/{id}', [RakutenController::class, 'update'])->name('rakuten.update')->where('id', '[0-9]+');
        Route::get('delete/{id}', [RakutenController::class, 'delete'])->name('rakuten.delete')->where('id', '[0-9]+');
        Route::get('destroy/{id}', [RakutenController::class, 'destroy'])->name('rakuten.destroy')->where('id', '[0-9]+');
        Route::get('recheck', [RakutenItemController::class, 'recheck'])->name('rakuten.recheck');
    });
    Route::group(['prefix' => 'items'], function () {
        Route::get('/{id}', [RakutenItemController::class, 'items'])->name('rakuten.items')->where('id', '[0-9]+');
    });
});

Route::group(['prefix' => 'digimart', 'middleware' => ['auth']], function () {
    Route::get('/', [DigimartsController::class, 'index'])->name('digimart.index');
    Route::group(['prefix' => 'reserve'], function () {
        Route::get('create', [DigimartsController::class, 'create'])->name('digimart.create');
        Route::post('store', [DigimartsController::class, 'store'])->name('digimart.store');
        Route::get('edit/{id}', [DigimartsController::class, 'edit'])->name('digimart.edit')->where('id', '[0-9]+');
        Route::post('update/{id}', [DigimartsController::class, 'update'])->name('digimart.update')->where('id', '[0-9]+');
        Route::get('delete/{id}', [DigimartsController::class, 'delete'])->name('digimart.delete')->where('id', '[0-9]+');
        Route::get('destroy/{id}', [DigimartsController::class, 'destroy'])->name('digimart.destroy')->where('id', '[0-9]+');
        Route::get('recheck', [DigimartItemsController::class, 'recheck'])->name('digimart.recheck');
    });
    Route::group(['prefix' => 'items'], function () {
        Route::get('/{id}', [DigimartItemsController::class, 'items'])->name('digimart.items')->where('id', '[0-9]+');
    });
});

Route::group(['prefix' => 'ebay', 'middleware' => ['auth']], function () {
    Route::get('/trading', [EbayItemController::class, 'index'])->name('ebay.index');
    Route::post('/trading/search/', [EbayItemController::class, 'search'])->name('ebay.search');
    Route::get('/trading/delete/{id}', [EbayItemController::class, 'delete'])->name('ebay.delete');
    Route::get('/trading/destroy/{id}', [EbayItemController::class, 'destroy'])->name('ebay.destroy');
});

Route::group(['prefix' => 'stock', 'middleware' => ['auth']], function () {
    Route::get('/search', [StocksController::class, 'search'])->name('stock.seach');
});


require __DIR__ . '/auth.php';
