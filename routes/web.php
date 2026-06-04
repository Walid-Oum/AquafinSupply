<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Userzone\CartController;
use App\Http\Controllers\Userzone\OrderController;
use App\Http\Controllers\Userzone\ProfileController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\Technician\MaterialController as TechnicianMaterialController;


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
/*
|--------------------------------------------------------------------------
| Administrator
|--------------------------------------------------------------------------
| Overzicht van alle bestellingen
*/
Route::get('/admin/orders', [AdminOrderController::class,'index'])
    ->name('admin.orders.index');
 // winkelmandje

    Route::post('/cart/add/{id}', [CartController::class, 'add'])
    ->name('cart.add');

Route::post('/cart/remove/{id}', [CartController::class, 'remove'])
    ->name('cart.remove');

Route::post('/bestelling/plaatsen', [OrderController::class, 'store'])
    ->name('orders.store');




//technieker
//technieker
Route::middleware('auth')->prefix('tickets')->group(function () {
    Route::get('/', [\App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');
    Route::get('/create', [\App\Http\Controllers\TicketController::class, 'create'])->name('tickets.create');
    Route::post('/', [\App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
});

//magazijnmedewerker

Route::middleware('auth')->prefix('magazijn/tickets')->group(function () {
    Route::get('/', [\App\Http\Controllers\TicketController::class, 'all'])->name('tickets.all');
    Route::get('/{ticket}', [\App\Http\Controllers\TicketController::class, 'showHouseware'])->name('tickets.showHouseware');

});

Route::get('/dashboard', function () {
    return view('userzone.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Materialen routes (Teamlid 1 - Admin)
Route::middleware(['auth'])->group(function () {
    Route::resource('materials', MaterialController::class);
});

// Technieker materialen routes
Route::prefix('technician')->name('technician.')->middleware(['auth'])->group(function () {
    Route::get('/materials', [TechnicianMaterialController::class, 'index'])->name('materials.index');
    Route::get('/materials/{id}', [TechnicianMaterialController::class, 'show'])->name('materials.show');
});

// Winkelmandje routes
Route::middleware(['auth'])->group(function () {
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
});

require __DIR__.'/auth.php';