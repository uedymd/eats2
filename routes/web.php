<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Rakuten\RakutenController;
use App\Http\Controllers\Rakuten\RakutenItemController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\EbayItemController;
use App\Http\Controllers\BrandSetController;

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
Route::redirect('/dashboard', '/rakuten')->middleware(['auth'])->name('dashboard');

Route::group(['prefix' => 'setting', 'middleware' => ['auth']], function () {
    Route::get('edit/{site}', [SettingController::class, 'edit'])->name('setting.edit');
    Route::post('update/{site}', [SettingController::class, 'update'])->name('setting.update');
    Route::get('brandset', [BrandSetController::class, 'index'])->name('setting.brandset.index');
    Route::get('brandset/create', [BrandSetController::class, 'create'])->name('setting.brandset.create');
    Route::post('brandset/store', [BrandSetController::class, 'store'])->name('setting.brandset.store');
});

Route::group(['prefix' => 'rakuten', 'middleware' => ['auth']], function () {
    Route::get('/', [RakutenController::class, 'index'])->name('rakuten.index');
    Route::group(['prefix' => 'reserve', 'middleware' => ['auth']], function () {
        Route::get('create', [RakutenController::class, 'create'])->name('rakuten.create');
        Route::post('store', [RakutenController::class, 'store'])->name('rakuten.store');
        Route::get('edit/{id}', [RakutenController::class, 'edit'])->name('rakuten.edit');
        Route::post('update/{id}', [RakutenController::class, 'update'])->name('rakuten.update');
        Route::get('delete/{id}', [RakutenController::class, 'delete'])->name('rakuten.delete');
        Route::get('destroy/{id}', [RakutenController::class, 'destroy'])->name('rakuten.destroy');
    });
    Route::group(['prefix' => 'items', 'middleware' => ['auth']], function () {
        Route::get('/{id}', [RakutenItemController::class, 'items'])->name('rakuten.items');
    });
});

Route::group(['prefix' => 'ebay', 'middleware' => ['auth']], function () {
    Route::get('/trading', [EbayItemController::class, 'index'])->name('ebay.index');
});


require __DIR__ . '/auth.php';
