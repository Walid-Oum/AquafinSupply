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
 * Deze controller bevat zowel de flow voor techniekers als de flow
 * voor magazijnmedewerkers.
 *
 * Techniekers kunnen:
 * - Eigen bestellingen bekijken
 * - Bestellingen plaatsen op basis van hun winkelmandje
 * - Details van hun eigen bestellingen bekijken
 *
 * Magazijnmedewerkers kunnen:
 * - Bestellingen van hun depot bekijken
 * - Bestellingen in detail openen
 * - Bestelstatussen aanpassen
 * - Hoeveelheden aanpassen
 * - Voorraad automatisch laten herberekenen
 * - Notificaties naar techniekers sturen bij statuswijzigingen
 */
class OrderController extends Controller
{
    /**
     * Toegestane statussen voor bestellingen in het magazijn.
     */
    private const WAREHOUSE_ORDER_STATUSES = [
        'Nieuw',
        'In voorbereiding',
        'Klaar om af te halen',
        'Afgehaald',
        'Geannuleerd',
    ];

    /**
     * Toon een overzicht van alle bestellingen van de ingelogde gebruiker.
     *
     * Enkel de bestellingen van de huidige technieker worden opgehaald.
     *
     * @return \Illuminate\View\View
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
     * Maak een nieuwe bestelling aan op basis van de inhoud van het winkelmandje.
     *
     * Controleert:
     * - Of een leverdatum aanwezig is
     * - Of de leverdatum niet in het verleden ligt
     * - Of de gebruiker gekoppeld is aan een depot
     * - Of het winkelmandje niet leeg is
     * - Of alle materialen nog actief zijn
     * - Of er voldoende voorraad is in het gekoppelde depot
     *
     * De bestelling wordt aangemaakt binnen een database-transactie.
     * Hierdoor blijven bestelling, order items en voorraad consistent
     * als er tijdens het proces een fout optreedt.
     *
     * @param Request $request De request met leverdatum en optionele opmerking.
     * @return \Illuminate\Http\RedirectResponse
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

        // Controleer of alle materialen nog bestaan en voldoende voorraad hebben.
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

        // Verstuur een notificatie naar alle magazijnmedewerkers van hetzelfde depot.
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
     * Toon de details van één bestelling van de ingelogde gebruiker.
     *
     * Er wordt gecontroleerd dat de bestelling effectief behoort tot
     * de ingelogde technieker.
     *
     * @param int|string $id Het ID van de bestelling.
     * @return \Illuminate\View\View
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
     * Toon alle bestellingen van het depot van de magazijnmedewerker.
     *
     * Alleen bestellingen van hetzelfde depot als de magazijnmedewerker
     * worden opgehaald.
     *
     * Ondersteunt zoeken op:
     * - Bestelnummer
     * - Naam van gebruiker
     *
     * @param Request $request De request met optionele zoekterm.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
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
     * - Materialen verwijderen door hoeveelheid op 0 te zetten
     *
     * De voorraad wordt automatisch herberekend op basis van het verschil
     * tussen de oude en nieuwe hoeveelheden.
     *
     * @param Request $request De request met status en optionele hoeveelheden.
     * @param Order $order De bestelling die aangepast wordt.
     * @return \Illuminate\Http\RedirectResponse
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

        try {
            // Voer alle voorraad- en orderwijzigingen atomair uit binnen één transactie.
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

        // Informeer de gebruiker wanneer de status van de bestelling gewijzigd is.
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
     * Toon de details van een bestelling voor een magazijnmedewerker.
     *
     * Enkel bestellingen van hetzelfde depot als de magazijnmedewerker
     * kunnen worden geopend.
     *
     * @param int|string $id Het ID van de bestelling.
     * @return \Illuminate\View\View
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
     * Toon het bewerkingsscherm voor een bestelling van het magazijn.
     *
     * Enkel bestellingen van hetzelfde depot als de magazijnmedewerker
     * kunnen worden bewerkt.
     *
     * @param int|string $id Het ID van de bestelling.
     * @return \Illuminate\View\View
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
