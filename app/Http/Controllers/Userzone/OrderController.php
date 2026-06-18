<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderItem;
use App\Models\Material;
use App\Models\MaterialStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\UserNotification;
use App\Models\User;
/**
 * Controller voor het beheren van bestellingen.
 *
 * Functionaliteiten:
 * - Bestellingen bekijken
 * - Bestellingen plaatsen
 * - Bestellingen raadplegen
 * - Magazijnbestellingen beheren
 * - Voorraad automatisch aanpassen
 * - Notificaties versturen bij wijzigingen
 */

class OrderController extends Controller
{

    private const WAREHOUSE_ORDER_STATUSES = [
        'Nieuw',
        'In voorbereiding',
        'Klaar om af te halen',
        'Afgehaald',
        'Geannuleerd',
    ];
    /**
     * Toon een overzicht van alle bestellingen
     * van de ingelogde gebruiker.
     */

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('location')
            ->latest()
            ->get();

        return view(
            'userzone.orders.index',
            compact('orders')
        );
    }
    /**
     * Maak een nieuwe bestelling aan op basis
     * van de inhoud van het winkelmandje.
     *
     * Controleert:
     * - Leverdatum
     * - Beschikbaarheid van materialen
     * - Voorraad van het gekoppelde depot
     *
     * Verlaagt de voorraad automatisch en
     * verstuurt een melding naar het magazijn.
     */

    public function store(Request $request)
    {
        $request->validate([
            'delivery_date' => 'required|date|after_or_equal:today',
        ], [
            'delivery_date.required' => 'Leverdatum is verplicht.',
            'delivery_date.after_or_equal' => 'Leverdatum mag niet in het verleden liggen.',
        ]);

        $user = Auth::user();

        if (!$user->location_id) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Er is geen depot gekoppeld aan je account. Contacteer een administrator.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Winkelmandje is leeg.');
        }
// Controleer of alle materialen nog bestaan
// en voldoende voorraad hebben.
        foreach ($cart as $item) {
            $material = Material::find($item['id']);

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
                    ->with('error', 'Een materiaal in uw winkelmandje is niet meer beschikbaar.');
            }

            $materialStock = MaterialStock::where('material_id', $material->id)
                ->where('location_id', $user->location_id)
                ->first();

            if (!$materialStock) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Dit materiaal is niet beschikbaar in jouw depot.');
            }

            if ($item['quantity'] > $materialStock->stock) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Onvoldoende voorraad in jouw depot voor ' . $material->name . '.');
            }
        }
// Maak bestelling aan binnen een database-transactie.
// Hierdoor blijft de voorraad consistent bij fouten.
        try {
            $order = DB::transaction(function () use ($cart, $user, $request) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'location_id' => $user->location_id,
                    'delivery_date' => $request->delivery_date,
                    'comment' => $request->comment,
                    'status' => 'Nieuw',
                ]);

                foreach ($cart as $item) {
                    $material = Material::findOrFail($item['id']);

                    $materialStock = MaterialStock::where('material_id', $material->id)
                        ->where('location_id', $user->location_id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($item['quantity'] > $materialStock->stock) {
                        throw new \Exception('Onvoldoende voorraad in jouw depot voor ' . $material->name . '.');
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'material_id' => $material->id,
                        'quantity' => $item['quantity'],
                    ]);

                    $materialStock->decrement('stock', $item['quantity']);
                }

                return $order;
            });
        } catch (\Exception $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        session()->forget('cart');


        $warehouseUsers = User::where('role', 'magazijn')
            ->where('location_id', $order->location_id)
            ->get();
// Verstuur een notificatie naar alle
// magazijnmedewerkers van hetzelfde depot.
        foreach ($warehouseUsers as $warehouseUser) {
            UserNotification::create([
                'user_id' => $warehouseUser->id,
                'title' => 'Nieuwe bestelling',
                'message' => 'Er is een nieuwe bestelling #' . $order->id . ' geplaatst door ' . $user->name . '.',
                'link' => route('magazijn.orders.show', $order->id),
            ]);
        }

        return redirect()
            ->route('orders.index')
            ->with('success', 'Bestelling succesvol geplaatst.');
    }
    /**
     * Toon de details van één bestelling
     * van de ingelogde gebruiker.
     */

    public function show($id)
    {
        $order = Order::with('items.material', 'location')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view(
            'userzone.orders.show',
            compact('order')
        );
    }
    /**
     * Toon alle bestellingen van het depot
     * van de magazijnmedewerker.
     *
     * Ondersteunt zoeken op:
     * - Bestelnummer
     * - Naam van gebruiker
     */
    public function warehouseIndex(Request $request)
    {
        $user = Auth::user();

        if (!$user->location_id) {
            return redirect()
                ->back()
                ->with('error', 'Er is geen depot gekoppeld aan je account. Contacteer een administrator.');
        }

        $query = Order::with(['user', 'location'])
            ->where('location_id', $user->location_id);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where(
                    'id',
                    'like',
                    '%' . $request->search . '%'
                )
                    ->orWhereHas(
                        'user',
                        function ($user) use ($request) {
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
    /**
     * Werk een bestelling bij vanuit het magazijn.
     *
     * Mogelijkheden:
     * - Status wijzigen
     * - Hoeveelheden aanpassen
     * - Materialen verwijderen
     *
     * De voorraad wordt automatisch herberekend.
     */
    public function warehouseUpdate(Request $request, Order $order)
    {
        if ($order->location_id !== Auth::user()->location_id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(self::WAREHOUSE_ORDER_STATUSES),
            ],
            'quantities' => ['nullable', 'array'],
            'quantities.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];
        $statusChanged = false;

        try {// Voer alle voorraad- en orderwijzigingen
// atomair uit binnen één transactie.
            DB::transaction(function () use ($validated, $order, $oldStatus, $newStatus, &$statusChanged) {
                $order->status = $newStatus;
                $order->save();

                $statusChanged = $oldStatus !== $newStatus;

                if (! empty($validated['quantities'])) {
                    foreach ($validated['quantities'] as $itemId => $newQuantity) {
                        $item = $order->items()
                            ->whereKey($itemId)
                            ->first();

                        if (! $item) {
                            abort(403);
                        }

                        $material = $item->material;

                        $materialStock = MaterialStock::where('material_id', $material->id)
                            ->where('location_id', $order->location_id)
                            ->lockForUpdate()
                            ->firstOrFail();

                        $oldQuantity = $item->quantity;
                        $newQuantity = (int) $newQuantity;

                        if ($newQuantity === 0) {
                            $materialStock->increment('stock', $oldQuantity);
                            $item->delete();

                            continue;
                        }

                        $difference = $newQuantity - $oldQuantity;

                        if ($difference > 0) {
                            if ($materialStock->stock < $difference) {
                                throw new \Exception(
                                    'Onvoldoende voorraad in jouw depot voor ' . $material->name . '.'
                                );
                            }

                            $materialStock->decrement('stock', $difference);
                        }

                        if ($difference < 0) {
                            $materialStock->increment('stock', abs($difference));
                        }

                        $item->update([
                            'quantity' => $newQuantity,
                        ]);
                    }
                }
            });
        } catch (\Exception $exception) {
            return redirect()
                ->back()
                ->with('error', $exception->getMessage());
        }
// Informeer de gebruiker wanneer
// de status van de bestelling gewijzigd is.
        if ($statusChanged) {
            UserNotification::create([
                'user_id' => $order->user_id,
                'title' => 'Bestelling bijgewerkt',
                'message' => 'Je bestelling #' . $order->id . ' kreeg status "' . $newStatus . '".',
                'link' => route('orders.show', $order->id),
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Bestelling succesvol gewijzigd.');
    }
    /**
     * Toon de details van een bestelling
     * voor een magazijnmedewerker.
     */
    public function warehouseShow($id)
    {
        $order = Order::with('items.material', 'user', 'location')
            ->where('location_id', Auth::user()->location_id)
            ->findOrFail($id);

        return view(
            'magazijn.orders.show',
            compact('order')
        );
    }
    /**
     * Toon het bewerkingsscherm voor een
     * bestelling van het magazijn.
     */
    public function warehouseEdit($id)
    {
        $order = Order::with('items.material', 'user', 'location')
            ->where('location_id', Auth::user()->location_id)
            ->findOrFail($id);

        return view(
            'magazijn.orders.edit',
            compact('order')
        );
    }
}
