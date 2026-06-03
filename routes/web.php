<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Userzone\CartController;
use App\Http\Controllers\Userzone\OrderController;
use App\Http\Controllers\Userzone\ProfileController;





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




Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('userzone.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
