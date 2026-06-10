<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;

class CartController extends Controller
{
    public function index()
    {
        return view('userzone.orders.cart');
    }

    public function add($id)
    {
        $material = Material::findOrFail($id);

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {

            $cart[$id]['quantity']++;

        } else {

           $cart[$id] = [
    'id' => $material->id,
    'name' => $material->name,
    'category' => $material->category,
    'stock' => $material->stock,
    'quantity' => 1,
];
        }

        session()->put('cart', $cart);

        return redirect()->back()
            ->with('success', 'Materiaal toegevoegd aan winkelmandje');
    }
  

    public function update(Request $request, $id)
{
    $request->validate([
        'quantity' => 'required|integer|min:1',
    ]);

    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {
 if($request->quantity > $cart[$id]['stock']){

    return redirect()
        ->back()
        ->with(
            'error',
            'Onvoldoende voorraad.'
        );

}
        $cart[$id]['quantity'] = $request->quantity;

        session()->put('cart', $cart);
    }

    return redirect()
        ->route('cart.index')
        ->with('success', 'Aantal bijgewerkt.');
}



    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {

            unset($cart[$id]);

            session()->put('cart', $cart);
        }

        return redirect()->back()
            ->with('success', 'Materiaal verwijderd');
    }
}
