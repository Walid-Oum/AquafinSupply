<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
{
    $orders = Order::where('user_id', Auth::id())
        ->latest()
        ->get();

    return view(
        'userzone.orders.index',
        compact('orders')
    );
}

    public function store(Request $request)
{

}

   use App\Models\Order;

public function show($id)
{
    $order = Order::with('items.material')
        ->findOrFail($id);

    return view(
        'userzone.orders.show',
        compact('order')
    );
}
}
