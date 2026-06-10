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

        if (!$material->is_active) {

    return redirect()
        ->back()
        ->withInput()
        ->with(
            'error',
            'Een materiaal in uw winkelmandje is niet meer beschikbaar.'
        );

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

public function warehouseIndex(Request $request)
{
    $query = Order::with('user');

    if ($request->search) {

       $query->where(function($q) use ($request){

    $q->where(
        'id',
        'like',
        '%' . $request->search . '%'
    )

    ->orWhereHas(
        'user',
        function($user) use ($request){

            $user->where(
                'name',
                'like',
                '%' . $request->search . '%'
            );

        }
    );

});

    }

    $orders = $query
        ->latest()
        ->get();

    return view(
        'magazijn.orders.index',
        compact('orders')
    );
}

public function warehouseUpdate(Request $request, Order $order)
{
    $request->validate([
        'status' => 'required',
    ]);

    $order->status = $request->status;
    $order->save();

    if ($request->has('quantities')) {

        foreach ($request->quantities as $itemId => $newQuantity) {

            $item = OrderItem::find($itemId);

            if (!$item) {
                continue;
            }

            $material = $item->material;

            $oldQuantity = $item->quantity;

           
            if ($newQuantity == 0) {

                $material->increment(
                    'stock',
                    $oldQuantity
                );

                $item->delete();

            } else {

                $difference = $newQuantity - $oldQuantity;

               
                if ($difference > 0) {

                    if ($material->stock < $difference) {

                        return redirect()
                            ->back()
                            ->with(
                                'error',
                                'Onvoldoende voorraad voor '
                                . $material->name
                            );
                    }

                    $material->decrement(
                        'stock',
                        $difference
                    );

                }

                
                elseif ($difference < 0) {

                    $material->increment(
                        'stock',
                        abs($difference)
                    );

                }

                $item->update([
                    'quantity' => $newQuantity
                ]);
            }
        }
    }

    return redirect()
        ->back()
        ->with(
            'success',
            'Bestelling succesvol gewijzigd.'
        );
}

public function warehouseShow($id)
{
    $order = Order::with('items.material', 'user')
        ->findOrFail($id);

    return view(
        'magazijn.orders.show',
        compact('order')
    );
}

public function warehouseEdit($id)
{
    $order = Order::with('items.material', 'user')
        ->findOrFail($id);

    return view(
        'magazijn.orders.edit',
        compact('order')
    );
}
}
