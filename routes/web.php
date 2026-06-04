<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Userzone\CartController;
use App\Http\Controllers\Userzone\OrderController;
use App\Http\Controllers\Userzone\ProfileController;
use App\Http\Controllers\MaterialController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Bestellingen
|--------------------------------------------------------------------------
| Routes voor het winkelmandje en het beheer van bestellingen.
*/
Route::get('/winkelmandje', [CartController::class,'index'])
    ->name('cart.index');

Route::get('/bestellingen', [OrderController::class,'index'])
    ->name('orders.index');

Route::get('/bestellingen/{id}', [OrderController::class,'show'])
    ->name('orders.show');

//tickets
Route::middleware('auth')->prefix('tickets')->group(function () {
    Route::get('/', [\App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');
    Route::get('/create', [\App\Http\Controllers\TicketController::class, 'create'])->name('tickets.create');
    Route::post('/', [\App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
});

Route::get('/dashboard', function () {
    return view('userzone.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Materialen routes (Teamlid 1)
Route::middleware(['auth'])->group(function () {
    Route::resource('materials', MaterialController::class);
});

require __DIR__.'/auth.php';