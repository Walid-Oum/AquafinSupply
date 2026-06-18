<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller voor het winkelmandje van techniekers.
 *
 * Deze controller beheert alle acties rond het winkelmandje
 * van de ingelogde technieker. Het winkelmandje wordt opgeslagen
 * in de sessie van de gebruiker.
 *
 * Functionaliteiten:
 * - Winkelmandje tonen
 * - Materiaal toevoegen aan winkelmandje
 * - Aantal aanpassen
 * - Materiaal verwijderen
 * - Winkelmandje leegmaken
 * - Voorraadcontrole per depot
 */
class CartController extends Controller
{
    /**
     * Toon het winkelmandje van de gebruiker.
     *
     * Voor het winkelmandje wordt weergegeven, wordt eerst
     * gecontroleerd of de materialen nog actief zijn en of de
     * voorraadgegevens nog actueel zijn.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->refreshCartStock();

        return view('userzone.orders.cart');
    }

    /**
     * Voeg een materiaal toe aan het winkelmandje.
     *
     * Deze methode controleert eerst of de gebruiker gekoppeld is
     * aan een depot, of het materiaal actief is en of er voldoende
     * voorraad beschikbaar is in het depot van de gebruiker.
     *
     * @param int|string $id Het ID van het materiaal.
     * @return mixed JSON-response of redirect, afhankelijk van de request.
     */
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

    /**
     * Werk de hoeveelheid van een materiaal in het winkelmandje bij.
     *
     * De methode valideert de nieuwe hoeveelheid en controleert opnieuw
     * of het materiaal nog actief en beschikbaar is in het depot van
     * de gebruiker.
     *
     * @param Request $request De request met de nieuwe hoeveelheid.
     * @param int|string $id Het ID van het materiaal.
     * @return mixed JSON-response of redirect, afhankelijk van de request.
     */
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

    /**
     * Verwijder een materiaal uit het winkelmandje.
     *
     * @param int|string $id Het ID van het materiaal.
     * @return mixed JSON-response of redirect, afhankelijk van de request.
     */
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

    /**
     * Maak het volledige winkelmandje leeg.
     *
     * @return mixed JSON-response of redirect, afhankelijk van de request.
     */
    public function clear()
    {
        session()->forget('cart');

        return $this->cartSuccess('Winkelmandje leeggemaakt.');
    }

    /**
     * Genereer een succesvolle response voor winkelmand-acties.
     *
     * Deze methode ondersteunt zowel JSON-responses voor AJAX
     * als gewone redirects voor klassieke formulierrequests.
     *
     * @param string $message De succesboodschap.
     * @param array $extraData Extra data die aan de response wordt toegevoegd.
     * @return mixed JSON-response of redirect.
     */
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

    /**
     * Genereer een foutmelding voor winkelmand-acties.
     *
     * Deze methode ondersteunt zowel JSON-responses voor AJAX
     * als gewone redirects voor klassieke formulierrequests.
     *
     * @param string $message De foutmelding.
     * @return mixed JSON-response of redirect.
     */
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

    /**
     * Geef het aantal unieke materialen in het winkelmandje terug.
     *
     * Hierbij wordt niet de totale hoeveelheid geteld, maar het aantal
     * verschillende materiaalsoorten in het winkelmandje.
     *
     * @return int Het aantal unieke materialen in het winkelmandje.
     */
    private function getCartCount(): int
    {
        return count(session('cart', []));
    }

    /**
     * Haal de voorraad van een materiaal op voor een specifiek depot.
     *
     * @param int $materialId Het ID van het materiaal.
     * @param int $locationId Het ID van het depot.
     * @return MaterialStock|null De voorraadrecord voor dit materiaal en depot.
     */
    private function getDepotStock(int $materialId, int $locationId): ?MaterialStock
    {
        return MaterialStock::where('material_id', $materialId)
            ->where('location_id', $locationId)
            ->first();
    }

    /**
     * Synchroniseer het winkelmandje met de actuele voorraad.
     *
     * Niet-beschikbare materialen worden verwijderd uit het winkelmandje.
     * Hoeveelheden worden aangepast wanneer ze hoger zijn dan de actuele
     * voorraad in het depot van de gebruiker.
     *
     * @return void
     */
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
