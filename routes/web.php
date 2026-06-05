<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Userzone\CartController;
use App\Http\Controllers\Userzone\OrderController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\Technician\MaterialController as TechnicianMaterialController;
use App\Http\Controllers\Admin\AdminOrderController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('userzone.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Technieker - Materialen
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('technician')
    ->name('technician.')
    ->group(function () {

        Route::get('/materials', [TechnicianMaterialController::class, 'index'])
            ->name('materials.index');

        Route::get('/materials/{id}', [TechnicianMaterialController::class, 'show'])
            ->name('materials.show');
    });

/*
|--------------------------------------------------------------------------
| Winkelmandje
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/winkelmandje', [CartController::class, 'index'])
        ->name('cart.index');

    Route::post('/cart/add/{id}', [CartController::class, 'add'])
        ->name('cart.add');

    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])
        ->name('cart.remove');

    Route::patch('/cart/update/{id}', [CartController::class, 'update'])
        ->name('cart.update');
});

/*
|--------------------------------------------------------------------------
| Bestellingen
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/bestellingen', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::get('/bestellingen/{id}', [OrderController::class, 'show'])
        ->name('orders.show');

    Route::post('/bestelling/plaatsen', [OrderController::class, 'store'])
        ->name('orders.store');
});

/*
|--------------------------------------------------------------------------
| Admin Materialen
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::resource('materials', MaterialController::class);
});

/*
|--------------------------------------------------------------------------
| Admin Bestellingen
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/admin/orders', [AdminOrderController::class, 'index'])
        ->name('admin.orders.index');

    Route::get('/admin/orders/{id}', [AdminOrderController::class, 'show'])
        ->name('admin.orders.show');
});

/*
|--------------------------------------------------------------------------
| Admin Gebruikersbeheer & Rollen
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::resource('users', \App\Http\Controllers\UserController::class);
    });

/*
|--------------------------------------------------------------------------
| Tickets Technieker
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('tickets')
    ->group(function () {

        Route::get('/', [\App\Http\Controllers\TicketController::class, 'index'])
            ->name('tickets.index');

        Route::get('/create', [\App\Http\Controllers\TicketController::class, 'create'])
            ->name('tickets.create');

        Route::post('/', [\App\Http\Controllers\TicketController::class, 'store'])
            ->name('tickets.store');
    });

/*
|--------------------------------------------------------------------------
| Tickets Magazijn
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('magazijn/tickets')
    ->group(function () {

        Route::get('/', [\App\Http\Controllers\TicketController::class, 'all'])
            ->name('tickets.warehouse.index');

        Route::get('/{ticket}', [\App\Http\Controllers\TicketController::class, 'showWarehouse'])
            ->name('tickets.warehouse.show');

        Route::patch('/{ticket}/status', [\App\Http\Controllers\TicketController::class, 'updateStatus'])
            ->name('tickets.warehouse.updateStatus');
    });

require __DIR__.'/auth.php';
