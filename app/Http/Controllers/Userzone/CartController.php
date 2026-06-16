<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $this->refreshCartStock();

        return view('userzone.orders.cart');
    }

    public function add($id)
    {
        $user = Auth::user();

        if (!$user->location_id) {
            return $this->cartError('Er is geen depot gekoppeld aan je account. Contacteer een administrator.');
        }

        $material = Material::findOrFail($id);

        if (!$material->is_active) {
            return $this->cartError('Dit materiaal is niet meer beschikbaar.');
        }

        $materialStock = $this->getDepotStock($material->id, $user->location_id);

        if (!$materialStock || $materialStock->stock <= 0) {
            return $this->cartError('Dit materiaal is niet beschikbaar in jouw depot.');
        }

        $cart = session()->get('cart', []);

        $currentQuantity = $cart[$id]['quantity'] ?? 0;
        $newQuantity = $currentQuantity + 1;

        if ($newQuantity > $materialStock->stock) {
            return $this->cartError('Onvoldoende voorraad in jouw depot voor ' . $material->name . '.');
        }

        $cart[$id] = [
            'id' => $material->id,
            'name' => $material->name,
            'category' => $material->category,
            'stock' => $materialStock->stock,
            'quantity' => $newQuantity,
        ];

        session()->put('cart', $cart);

        return $this->cartSuccess('Materiaal toegevoegd aan winkelmandje.', [
            'item_id' => $id,
            'quantity' => $newQuantity,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        if (!$user->location_id) {
            return $this->cartError('Er is geen depot gekoppeld aan je account. Contacteer een administrator.');
        }

        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return $this->cartError('Dit materiaal zit niet in je winkelmandje.');
        }

        $material = Material::findOrFail($id);

        if (!$material->is_active) {
            unset($cart[$id]);
            session()->put('cart', $cart);

            return $this->cartError('Dit materiaal is niet meer beschikbaar en werd uit je winkelmandje verwijderd.');
        }

        $materialStock = $this->getDepotStock($material->id, $user->location_id);

        if (!$materialStock || $materialStock->stock <= 0) {
            unset($cart[$id]);
            session()->put('cart', $cart);

            return $this->cartError('Dit materiaal is niet meer beschikbaar in jouw depot.');
        }

        if ($request->quantity > $materialStock->stock) {
            return $this->cartError('Onvoldoende voorraad in jouw depot. Beschikbaar: ' . $materialStock->stock . '.');
        }

        $cart[$id]['name'] = $material->name;
        $cart[$id]['category'] = $material->category;
        $cart[$id]['stock'] = $materialStock->stock;
        $cart[$id]['quantity'] = (int) $request->quantity;

        session()->put('cart', $cart);

        return $this->cartSuccess('Aantal bijgewerkt.', [
            'item_id' => $id,
            'quantity' => (int) $request->quantity,
        ]);
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return $this->cartSuccess('Materiaal verwijderd uit winkelmandje.', [
            'item_id' => $id,
        ]);
    }

    public function clear()
    {
        session()->forget('cart');

        return $this->cartSuccess('Winkelmandje leeggemaakt.');
    }

    private function cartSuccess(string $message, array $extraData = [])
    {
        $cart = session()->get('cart', []);

        $data = array_merge([
            'success' => true,
            'message' => $message,
            'cart_count' => $this->getCartCount(),
            'cart_empty' => count($cart) === 0,
        ], $extraData);

        if (request()->expectsJson()) {
            return response()->json($data);
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }

    private function cartError(string $message)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'cart_count' => $this->getCartCount(),
            ], 422);
        }

        return redirect()
            ->back()
            ->with('error', $message);
    }

    private function getCartCount(): int
    {
        return collect(session('cart', []))->sum('quantity');
    }

    private function getDepotStock(int $materialId, int $locationId): ?MaterialStock
    {
        return MaterialStock::where('material_id', $materialId)
            ->where('location_id', $locationId)
            ->first();
    }

    private function refreshCartStock(): void
    {
        $user = Auth::user();

        if (!$user || !$user->location_id) {
            return;
        }

        $cart = session()->get('cart', []);

        foreach ($cart as $id => $item) {
            $material = Material::find($id);

            if (!$material || !$material->is_active) {
                unset($cart[$id]);
                continue;
            }

            $materialStock = $this->getDepotStock($material->id, $user->location_id);

            if (!$materialStock || $materialStock->stock <= 0) {
                unset($cart[$id]);
                continue;
            }

            $cart[$id]['name'] = $material->name;
            $cart[$id]['category'] = $material->category;
            $cart[$id]['stock'] = $materialStock->stock;

            if ($cart[$id]['quantity'] > $materialStock->stock) {
                $cart[$id]['quantity'] = $materialStock->stock;
            }
        }

        session()->put('cart', $cart);
    }
}
