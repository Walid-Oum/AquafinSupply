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
        'delivery_date' => 'required|date|after_or_equal:today',
    ], [
        'delivery_date.required' => 'Leverdatum is verplicht.',
        'delivery_date.after_or_equal' => 'Leverdatum mag niet in het verleden liggen.',
    ]);

    $cart = session()->get('cart', []);

    if (empty($cart)) {

        return redirect()
    ->back()
    ->withInput()
    ->with('error', 'Winkelmandje is leeg.');
    }

    foreach ($cart as $item) {

        $material = \App\Models\Material::find($item['id']);

        if (!$material) {

            return redirect()
    ->back()
    ->withInput()
    ->with('error', 'Materiaal bestaat niet meer.');
        }

        if ($item['quantity'] > $material->stock) {

           return redirect()
    ->back()
    ->withInput()
    ->with('error', 'Onvoldoende voorraad');
        }
    }

    $order = Order::create([
        'user_id' => Auth::id(),
        'delivery_date' => $request->delivery_date,
        'comment' => $request->comment,
        'status' => 'Nieuw',
    ]);

    foreach ($cart as $item) {

        $material = \App\Models\Material::find($item['id']);

        OrderItem::create([
            'order_id' => $order->id,
            'material_id' => $material->id,
            'quantity' => $item['quantity'],
        ]);

        $material->decrement('stock', $item['quantity']);
    }

    session()->forget('cart');

    return redirect()
        ->route('orders.index')
        ->with('success', 'Bestelling succesvol geplaatst.');
}



public function show($id)
{
    $order = Order::with('items.material')
    ->where('user_id', Auth::id())
    ->findOrFail($id);

    return view(
        'userzone.orders.show',
        compact('order')
    );
}
}
