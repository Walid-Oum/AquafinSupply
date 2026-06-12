<?php

use App\Http\Controllers\PasswordChangeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Userzone\CartController;
use App\Http\Controllers\Userzone\OrderController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\Technician\MaterialController as TechnicianMaterialController;
use App\Http\Controllers\Admin\AdminOrderController;

Route::redirect('/', '/login');

/*
|--------------------------------------------------------------------------
| Dashboard & Profiel (Toegankelijk voor IEDEREEN die ingelogd is)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'password.changed', 'verified'])->group(function () {

    Route::get('/dashboard', function () {

        if (auth()->user()->role == 'technieker') {
            return redirect()->route('technician.materials.index');
        }

        if (auth()->user()->role == 'magazijn') {
            return redirect()->route('magazijn.materials.index');
        }

        if (auth()->user()->role == 'admin') {
            return redirect()->route('materials.index');
        }

    })->name('dashboard');

    Route::get('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'destroy'])->name('profile.destroy');
});
/*
|--------------------------------------------------------------------------
| ZONE: Technieker (Alleen Techniekers en Admins mogen hierbij)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'password.changed', 'role:technieker,admin'])->group(function () {

    // Technieker - Materialen bekijken
    Route::prefix('technician')->name('technician.')->group(function () {
        Route::get('/materials', [TechnicianMaterialController::class, 'index'])->name('materials.index');
        Route::get('/materials/{id}', [TechnicianMaterialController::class, 'show'])->name('materials.show');
    });

    // Winkelmandje van de technieker
    Route::get('/winkelmandje', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');

    // Bestellingen van de technieker
    Route::get('/bestellingen', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/bestellingen/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/bestelling/plaatsen', [OrderController::class, 'store'])->name('orders.store');

    // Eigen tickets aanmaken/bekijken
    Route::prefix('tickets')->group(function () {
        Route::get('/', [\App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');
        Route::get('/create', [\App\Http\Controllers\TicketController::class, 'create'])->name('tickets.create');
        Route::post('/', [\App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
    });
});

/*
|--------------------------------------------------------------------------
| ZONE: Magazijn (Alleen Magazijnmedewerkers en Admins mogen hierbij)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'password.changed', 'role:magazijn,admin'])->group(function () {

    // Tickets verwerken in het magazijn
    Route::prefix('magazijn/tickets')->group(function () {
        Route::get('/', [\App\Http\Controllers\TicketController::class, 'all'])->name('tickets.warehouse.index');
        Route::get('/{ticket}', [\App\Http\Controllers\TicketController::class, 'showWarehouse'])->name('tickets.warehouse.show');
        Route::patch('/{ticket}/status', [\App\Http\Controllers\TicketController::class, 'updateStatus'])->name('tickets.warehouse.updateStatus');
    });
});


/*
|--------------------------------------------------------------------------
| ZONE: Magazijn (Alleen Magazijnmedewerkers )
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'password.changed', 'role:magazijn'])->group(function () {

    Route::get(
        '/magazijn/bestellingen',
        [App\Http\Controllers\Userzone\OrderController::class, 'warehouseIndex']
    )->name('magazijn.orders.index');

    Route::patch(
        '/magazijn/bestellingen/{order}',
        [App\Http\Controllers\Userzone\OrderController::class, 'warehouseUpdate']
    )->name('magazijn.orders.update');

    Route::get(
        '/magazijn/voorraad',
        [MaterialController::class, 'warehouseIndex']
    )->name('magazijn.materials.index');

    Route::patch(
        '/magazijn/voorraad/{id}',
        [MaterialController::class, 'warehouseUpdate']
    )->name('magazijn.materials.update');

    Route::get(
        '/magazijn/material/{id}',
        [MaterialController::class, 'show']
    )->name('magazijn.materials.show');

    Route::get(
        '/magazijn/bestellingen/{id}',
        [App\Http\Controllers\Userzone\OrderController::class, 'warehouseShow']
    )->name('magazijn.orders.show');

    Route::get(
        '/magazijn/bestellingen/{id}/edit',
        [App\Http\Controllers\Userzone\OrderController::class, 'warehouseEdit']
    )->name('magazijn.orders.edit');


});
/*
|--------------------------------------------------------------------------
| ZONE: Admin (STRIKT ALLEEN VOOR ADMINS - Technieker en Magazijn worden geweigerd)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth',  'password.changed', 'role:admin'])->group(function () {

    // Admin Materialenbeheer (CRUD)
    Route::resource('materials', MaterialController::class);

    // Admin Bestellingenoverzicht
    Route::get('/admin/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/admin/orders/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');

    // Admin Gebruikersbeheer & Rollen toewijzen
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
    });
});

Route::middleware(['auth',  'password.changed',  'role:technieker,magazijn'])
    ->get('/overstromingsrisico', [\App\Http\Controllers\FloodRiskController::class, 'index'])
    ->name('flood-risk.index');



Route::middleware(['auth', 'password.changed', 'role:admin'])->group(function () {
    Route::get('/admin/overstromingsrisico', [\App\Http\Controllers\Admin\FloodRiskController::class, 'index'])
        ->name('admin.flood-risk.index');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/wachtwoord-instellen', [PasswordChangeController::class, 'edit'])
        ->name('password.change');

    Route::patch('/wachtwoord-instellen', [PasswordChangeController::class, 'update'])
        ->name('password.change.update');
});

Route::get('/api/search-materials', [App\Http\Controllers\MaterialController::class, 'searchSuggestions'])->middleware('auth', 'password.changed')->name('api.materials.search');
require __DIR__ . '/auth.php';
