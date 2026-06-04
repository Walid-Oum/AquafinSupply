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
                'quantity' => 1,
            ];

        }

        session()->put('cart', $cart);

        return redirect()->back()
            ->with('success', 'Materiaal toegevoegd aan winkelmandje');
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
