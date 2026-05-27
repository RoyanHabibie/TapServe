<?php

use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\TableController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin/dashboard', [AdminController::class, 'index'])->middleware('role:admin,owner')->name('admin.dashboard');
    });

// Kitchen routes (dilindungi role kitchen)
Route::middleware(['auth', 'role:kitchen'])->group(function () {
    Route::get('/kitchen/dashboard', [KitchenController::class, 'index'])->name('kitchen.dashboard');
    Route::post('/kitchen/order/{order}/status', [KitchenController::class, 'updateStatus'])->name('kitchen.order.status');
});

// Cashier routes
Route::middleware(['auth', 'role:cashier'])->group(function () {
    Route::get('/cashier/dashboard', [CashierController::class, 'index'])->name('cashier.dashboard');
    // Nanti payment route ditambahkan
});

Route::prefix('admin')->middleware(['auth', 'role:admin,owner'])->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('menus', MenuController::class)->except(['show']);
    Route::resource('tables', TableController::class)->except(['show']);
});

Route::get('/order/{token?}', [PublicOrderController::class, 'showMenu'])->name('public.menu');
Route::post('/cart/add/{token?}', [PublicOrderController::class, 'addToCart'])->name('public.cart.add');
Route::get('/cart/{token?}', [PublicOrderController::class, 'viewCart'])->name('public.cart');
Route::post('/cart/update/{token?}', [PublicOrderController::class, 'updateCart'])->name('public.cart.update');
Route::post('/cart/remove/{token?}', [PublicOrderController::class, 'removeFromCart'])->name('public.cart.remove');
Route::get('/checkout/{token?}', [PublicOrderController::class, 'checkout'])->name('public.checkout');
Route::post('/place-order/{token?}', [PublicOrderController::class, 'placeOrder'])->name('public.place.order');
Route::get('/order-status/{order}', [PublicOrderController::class, 'orderStatus'])->name('public.order.status');

Route::get('/sessions', [SessionController::class, 'index'])->name('admin.sessions.index');
Route::get('/sessions/{id}/close', [SessionController::class, 'close'])->name('admin.sessions.close');
Route::get('/sessions/{id}/cancel', [SessionController::class, 'cancel'])->name('admin.sessions.cancel');

require __DIR__ . '/auth.php';
