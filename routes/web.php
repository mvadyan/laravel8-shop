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

Route::group([
    'as' => 'catalog.',
    'prefix' => 'catalog',
], function () {
    Route::get('index', [\App\Http\Controllers\CatalogController::class, 'index'])
        ->name('index');
    Route::get('category/{category:slug}', [\App\Http\Controllers\CatalogController::class, 'category'])
        ->name('category');
    Route::get('brand/{brand:slug}', [\App\Http\Controllers\CatalogController::class, 'brand'])
        ->name('brand');
    Route::get('product/{product:slug}', [\App\Http\Controllers\CatalogController::class, 'product'])
        ->name('product');
    Route::get('search', [\App\Http\Controllers\CatalogController::class, 'search'])
        ->name('search');
}
);

Route::group([
    'as' => 'basket.',
    'prefix' => 'basket',
], function () {
    Route::get('index', [\App\Http\Controllers\BasketController::class, 'index'])
        ->name('index');
    Route::get('checkout', [\App\Http\Controllers\BasketController::class, 'checkout'])
        ->name('checkout');
    Route::post('add/{id}', [\App\Http\Controllers\BasketController::class, 'add'])
        ->where('id', '[0-9]+')
        ->name('add');
    Route::post('plus/{id}', [\App\Http\Controllers\BasketController::class, 'plus'])
        ->where('id', '[0-9]+')
        ->name('plus');
    Route::post('minus/{id}', [\App\Http\Controllers\BasketController::class, 'minus'])
        ->where('id', '[0-9]+')
        ->name('minus');
    Route::post('remove/{id}', [\App\Http\Controllers\BasketController::class, 'remove'])
        ->where('id', '[0-9]+')
        ->name('remove');
    Route::post('clear', [\App\Http\Controllers\BasketController::class, 'clear'])
        ->name('clear');
    Route::post('saveorder', [\App\Http\Controllers\BasketController::class, 'saveOrder'])->name('saveorder');
    Route::get('success', [\App\Http\Controllers\BasketController::class, 'success'])
        ->name('success');
    Route::post('profile', [\App\Http\Controllers\BasketController::class, 'profile'])
        ->name('profile');

});

Route::name('user.')->prefix('user')->group(function () {
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
