<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderItem;

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
   $request->validate([
    'delivery_date' => 'required|date|after:today',
    'comment' => 'nullable|string'
]);

    $cart = session()->get('cart', []);

    if (empty($cart)) {

        return redirect()->back()
            ->with('error', 'Winkelmandje is leeg');

    }

    $order = Order::create([

        'user_id' => Auth::id(),
        'delivery_date' => $request->delivery_date,
        'comment' => $request->comment,
        'status' => 'Nieuw',

    ]);

    foreach ($cart as $item) {

        OrderItem::create([

            'order_id' => $order->id,
            'material_id' => $item['id'],
            'quantity' => $item['quantity'],

        ]);

    }

    session()->forget('cart');

    return redirect()
        ->route('orders.index')
        ->with('success', 'Bestelling succesvol geplaatst');
}



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
