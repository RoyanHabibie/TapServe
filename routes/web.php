<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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


    Route::get('/admin/dashboard', fn() => 'Admin Dashboard')->name('admin.dashboard');
    Route::get('/cashier/dashboard', fn() => 'Cashier Dashboard')->name('cashier.dashboard');
    Route::get('/kitchen/dashboard', fn() => 'Kitchen Dashboard')->name('kitchen.dashboard');
});


require __DIR__ . '/auth.php';
