<?php

use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\QrisController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\UserController;

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

    // Admin dashboard (gunakan ReportController)
    Route::get('/admin/dashboard', [ReportController::class, 'dashboard'])
        ->middleware('role:admin,owner')
        ->name('admin.dashboard');
});

// Kitchen routes (dilindungi role kitchen)
Route::middleware(['auth', 'role:kitchen'])->group(function () {
    Route::get('/kitchen/dashboard', [KitchenController::class, 'index'])->name('kitchen.dashboard');
    Route::post('/kitchen/order/{order}/status', [KitchenController::class, 'updateStatus'])->name('kitchen.order.status');
    // AJAX order status untuk customer
    Route::get('/order-status-ajax/{order}', [PublicOrderController::class, 'ajaxOrderStatus'])->name('public.order.status.ajax');
    // AJAX orders untuk kitchen
    Route::get('/kitchen/orders-ajax', [KitchenController::class, 'ajaxOrders'])->name('kitchen.orders.ajax');
});

// Cashier routes
Route::middleware(['auth', 'role:cashier'])->group(function () {
    Route::get('/cashier/dashboard', [CashierController::class, 'index'])->name('cashier.dashboard');
    // Nanti payment route ditambahkan
});

Route::prefix('admin')->middleware(['auth', 'role:admin,owner'])->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('menus', MenuController::class)->except(['show']);
    Route::get('/tables/qrcodes', [TableController::class, 'printAllQr'])->name('tables.qrcodes');
    Route::resource('tables', TableController::class)->except(['show']);
    Route::get('/tables/{table}/qrcode', [TableController::class, 'printQr'])->name('tables.qrcode');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::resource('users', UserController::class)->except(['show']);
    Route::get('/settings/shop', [ShopController::class, 'show'])->name('settings.shop');
    Route::post('/settings/shop', [ShopController::class, 'update'])->name('settings.shop.update');
    Route::delete('/settings/shop/logo', [ShopController::class, 'destroyLogo'])->name('settings.shop.logo.destroy');
    Route::get('/settings/qris', [QrisController::class, 'show'])->name('settings.qris');
    Route::post('/settings/qris', [QrisController::class, 'update'])->name('settings.qris.update');
    Route::delete('/settings/qris', [QrisController::class, 'destroy'])->name('settings.qris.destroy');
    Route::resource('payment-methods', PaymentMethodController::class)->except(['show']);
    Route::post('/payment-methods/{paymentMethod}/toggle', [PaymentMethodController::class, 'toggle'])->name('payment-methods.toggle');
});

// Payment routes (bisa diakses cashier, admin, owner)
Route::prefix('admin')->middleware(['auth', 'role:admin,owner,cashier'])->name('admin.')->group(function () {
    Route::get('/payments/{session}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{session}', [PaymentController::class, 'store'])->name('payments.store');
    Route::post('/payments/{session}/manual-order', [PaymentController::class, 'addManualOrder'])->name('payments.manual-order');
});

Route::get('/order/{token?}', [PublicOrderController::class, 'showMenu'])->name('public.menu');
Route::get('/session/{token}', [PublicOrderController::class, 'sessionSummary'])->name('public.session');
Route::get('/session/{token}/status', [PublicOrderController::class, 'ajaxSessionStatus'])->name('public.session.status');
Route::post('/session/{token}/request-payment', [PublicOrderController::class, 'requestPayment'])->name('public.session.request-payment');
Route::post('/cart/add/{token?}', [PublicOrderController::class, 'addToCart'])->name('public.cart.add');
Route::get('/cart/{token?}', [PublicOrderController::class, 'viewCart'])->name('public.cart');
Route::post('/cart/update/{token?}', [PublicOrderController::class, 'updateCart'])->name('public.cart.update');
Route::post('/cart/remove/{token?}', [PublicOrderController::class, 'removeFromCart'])->name('public.cart.remove');
Route::post('/cart/item-type/{token?}', [PublicOrderController::class, 'updateCartItemType'])->name('public.cart.item-type');
Route::get('/checkout/{token?}', [PublicOrderController::class, 'checkout'])->name('public.checkout');
Route::post('/place-order/{token?}', [PublicOrderController::class, 'placeOrder'])->name('public.place.order');
Route::get('/order-status/{order}', [PublicOrderController::class, 'orderStatus'])->name('public.order.status');

Route::prefix('admin')->middleware(['auth', 'role:admin,owner,cashier'])->name('admin.')->group(function () {
    Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index');
    Route::get('/sessions/{id}/close', [SessionController::class, 'close'])->name('sessions.close');
    Route::get('/sessions/{id}/cancel', [SessionController::class, 'cancel'])->name('sessions.cancel');
});

require __DIR__ . '/auth.php';
