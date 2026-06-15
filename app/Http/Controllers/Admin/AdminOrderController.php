<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');
     // Filteren op status indien meegegeven en niet "all"
        if ($request->status && $request->status !== 'all') {

            $query->where(
                'status',
                $request->status
            );

        }
// Haal de resultaten op, gesorteerd op de meest recente bestellingen
        $orders = $query
            ->latest()
            ->get();

        return view(
            'admin.orders.index',
            compact('orders')
        );
    }

    public function show($id)
{
    // Haal de bestelling op met de bijbehorende gebruiker en items, inclusief het materiaal van elk item
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
