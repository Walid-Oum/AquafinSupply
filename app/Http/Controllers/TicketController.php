<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    private const TICKET_STATUSES = [
        'Open',
        'In behandeling',
        'Opgelost',
    ];
    public function index()
    {
        $tickets = Ticket::with(['order', 'location'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('tickets.index', [
            'tickets' => $tickets
        ]);
    }

    public function create()
    {
        $orders = Order::with('location')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('tickets.create', [
            'orders' => $orders
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        $order = Order::where('id', $validated['order_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        Ticket::create([
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'location_id' => $order->location_id,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'Open',
        ]);

        return redirect()
            ->route('tickets.index')
            ->with('success', 'Ticket succesvol aangemaakt.');
    }

    public function all()
    {
        $user = auth()->user();

        if (! $user->location_id) {
            return redirect()
                ->back()
                ->with('error', 'Er is geen depot gekoppeld aan je account. Contacteer een administrator.');
        }

        $tickets = Ticket::with(['user', 'order', 'location'])
            ->where('location_id', $user->location_id)
            ->latest()
            ->get();

        return view('tickets.warehouse.all', [
            'tickets' => $tickets
        ]);
    }

    public function showWarehouse(Ticket $ticket)
    {
        if ($ticket->location_id !== auth()->user()->location_id) {
            abort(403);
        }

        $ticket->load(['user', 'order', 'location']);

        return view('tickets.warehouse.show', [
            'ticket' => $ticket
        ]);
    }

    public function updateStatus(Ticket $ticket, Request $request)
    {
        if ($ticket->location_id !== auth()->user()->location_id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(self::TICKET_STATUSES),
            ],
            'warehouse_note' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $ticket->update([
            'status' => $validated['status'],
            'warehouse_note' => $validated['warehouse_note'] ?? null,
        ]);

        return redirect()
            ->route('tickets.warehouse.show', $ticket)
            ->with('success', 'Ticket succesvol bijgewerkt.');
    }
}
