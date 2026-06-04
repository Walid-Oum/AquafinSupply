<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Toont het winkelmandje van de technieker.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('userzone.orders.cart', compact('cart'));
    }

    /**
     * Voegt een materiaal toe aan het winkelmandje.
     */
    public function add($id)
    {
        $material = \App\Models\Material::findOrFail($id);
        
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
        
        return redirect()->back()->with('success', $material->name . ' toegevoegd aan winkelmandje!');
    }

    /**
     * Verwijdert een materiaal uit het winkelmandje.
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        
        return redirect()->route('cart.index')->with('success', 'Item verwijderd!');
    }
}