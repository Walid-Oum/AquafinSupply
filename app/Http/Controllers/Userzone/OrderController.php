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

class OrderController extends Controller
{

    private const WAREHOUSE_ORDER_STATUSES = [
        'Nieuw',
        'In voorbereiding',
        'Klaar om af te halen',
        'Afgehaald',
        'Geannuleerd',
    ];
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
