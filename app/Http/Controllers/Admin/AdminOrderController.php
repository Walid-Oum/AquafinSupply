<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')
            ->latest()
            ->get();

        return view(
            'admin.orders.index',
            compact('orders')
        );
    }
    public function show($id)
{
    $order = \App\Models\Order::with([
        'user',
        'items.material'
    ])->findOrFail($id);

    return view(
        'admin.orders.show',
        compact('order')
    );
}
}