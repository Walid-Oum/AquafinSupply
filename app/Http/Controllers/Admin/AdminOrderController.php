<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Location;

/**
 * AdminOrderController
 *
 * Beheer van bestellingen voor administrators.
 *
 * Functionaliteiten:
 * - Overzicht van alle bestellingen
 * - Filteren op status
 * - Filteren op depotlocatie
 * - Detailweergave van een bestelling
 *
 * User Stories:
 *
 * - US 29 Admin: Bestellingen raadplegen
 */


class AdminOrderController extends Controller
{
    /**
     * Toon een overzicht van alle bestellingen.
     *
     * De administrator kan bestellingen filteren
     * op status en depotlocatie.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */

    public function index(Request $request)
    {
        $query = Order::with('user.location');
        // Filter bestellingen op gekozen status
        if ($request->status && $request->status !== 'all') {

            $query->where(
                'status',
                $request->status
            );

        }if ($request->filled('location_id')) {

    $query->whereHas('user', function ($q) use ($request) {

        $q->where(
            'location_id',
            $request->location_id
        );

    });

}
// Sorteer bestellingen van nieuw naar oud
        $orders = $query
            ->latest()
            ->get();

$locations = Location::all();
       return view(
    'admin.orders.index',
    compact(
        'orders',
        'locations'
    )
);
    }
    /**
     * Toon de details van één bestelling.
     *
     * Laadt de gebruiker en alle bestelde materialen
     * zodat de administrator de volledige bestelling
     * kan raadplegen.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */

    public function show($id)
{
// Laad bestelling inclusief gebruiker en gekoppelde materialen
    $order = \App\Models\Order::with([
        'user',
        'items.material' // de materiaal van elk item wordt ook opgehaald
    ])->findOrFail($id);

    return view(
        'admin.orders.show',
        compact('order')
    );
}
}
