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

        if ($request->status && $request->status !== 'all') {

            $query->where(
                'status',
                $request->status
            );

        }

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
