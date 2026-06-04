<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;

class CartController extends Controller
{

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
            'quantity' => 1,
        ];

    }

    session()->put('cart', $cart);

    return redirect()->back();
}

public function remove($id)
{
    $cart = session()->get('cart', []);

    if (isset($cart[$id])) {

        unset($cart[$id]);

        session()->put('cart', $cart);
    }

    return redirect()->back();
}
    public function index()
    {
         /**
     * Toont het winkelmandje van de technieker.
     */
        $materials = Material::where('is_active', true)->get();

    return view(
        'userzone.orders.cart',
        compact('materials')
    );
    }
}
