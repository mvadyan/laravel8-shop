<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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


//маршрут для главной страницы
Route::get('/', \App\Http\Controllers\IndexController::class)->name('index');

Route::get('page/{page:slug}', \App\Http\Controllers\PageController::class)->name('page.show');

Route::get('/catalog/index', [\App\Http\Controllers\CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/category/{category:slug}', [\App\Http\Controllers\CatalogController::class, 'category'])->name('catalog.category');
Route::get('/catalog/brand/{brand:slug}', [\App\Http\Controllers\CatalogController::class, 'brand'])->name('catalog.brand');
Route::get('/catalog/product/{product:slug}', [\App\Http\Controllers\CatalogController::class, 'product'])->name('catalog.product');


Route::get('/basket/index', [\App\Http\Controllers\BasketController::class, 'index'])->name('basket.index');
Route::get('/basket/checkout', [\App\Http\Controllers\BasketController::class, 'checkout'])->name('basket.checkout');

Route::post('/basket/add/{id}', [\App\Http\Controllers\BasketController::class, 'add'])
    ->where('id', '[0-9]+')
    ->name('basket.add');

Route::post('basket/plus/{id}', [\App\Http\Controllers\BasketController::class, 'plus'])
    ->where('id', '[0-9]+')
    ->name('basket.plus');

Route::post('basket/minus/{id}', [\App\Http\Controllers\BasketController::class, 'minus'])
    ->where('id', '[0-9]+')
    ->name('basket.minus');

Route::post('basket/remove/{id}', [\App\Http\Controllers\BasketController::class, 'remove'])
    ->where('id', '[0-9]+')
    ->name('basket.remove');

Route::post('basket/clear', [\App\Http\Controllers\BasketController::class, 'clear'])
    ->name('basket.clear');

Route::post('/basket/saveorder', [\App\Http\Controllers\BasketController::class, 'saveOrder'])->name('basket.saveorder');

Route::get('/basket/success', [\App\Http\Controllers\BasketController::class, 'success'])
    ->name('basket.success');

Route::post('/basket/profile', [\App\Http\Controllers\BasketController::class, 'profile'])
    ->name('basket.profile');

Route::name('user.')->prefix('user')->group(function () {
    //Route::get('index', [\App\Http\Controllers\UserController::class, 'index'])->name('index');
    Auth::routes();
});

Route::name('admin.')->prefix('admin')->group(function () {
    Route::get('index', \App\Http\Controllers\Admin\IndexController::class)->name('index');
});

/////////////////////
Route::group([
    'as' => 'admin.',
    'prefix' => 'admin',
    'middleware' => ['auth', 'admin']
], function () {
    Route::get('index', \App\Http\Controllers\Admin\IndexController::class)->name('index');
    Route::resource('category', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('brand', \App\Http\Controllers\Admin\BrandController::class);
    Route::resource('product', \App\Http\Controllers\Admin\ProductController::class);

    Route::get('product/category/{category}', [\App\Http\Controllers\Admin\ProductController::class, 'category'])
        ->name('product.category');

    Route::resource('order', \App\Http\Controllers\Admin\OrderController::class)
        ->except(['create', 'store', 'destroy']);

    Route::resource('user', \App\Http\Controllers\Admin\UserController::class)
        ->except(['create', 'store', 'show', 'destroy']);

    Route::resource('page', \App\Http\Controllers\Admin\PageController::class);

    Route::post('page/upload/image', [\App\Http\Controllers\Admin\PageController::class, 'uploadImage'])
        ->name('page.upload.image');
    Route::post('page/remove/image', [\App\Http\Controllers\Admin\PageController::class, 'removeImage'])
        ->name('page.remove.image');
});
//////////////////////
Route::group([
    'as' => 'user.',
    'prefix' => 'user',
    'middleware' => ['auth']
], function () {
    Route::get('index', [\App\Http\Controllers\UserController::class, 'index'])->name('index');
    Route::resource('profile', \App\Http\Controllers\ProfileController::class);
    Route::get('order', [\App\Http\Controllers\OrderController::class, 'index'])
        ->name('order.index');
    Route::get('order/{order}', [\App\Http\Controllers\OrderController::class, 'show'])
        ->name('order.show');
});


