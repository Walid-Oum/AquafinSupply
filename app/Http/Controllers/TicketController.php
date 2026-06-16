<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Support\FuzzySearch;
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
            'tickets' => $tickets,
        ]);
    }

    public function create()
    {
        $orders = Order::with('location')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('tickets.create', [
            'orders' => $orders,
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

    public function all(Request $request)
    {
        $user = auth()->user();

        if (! $user->location_id) {
            return redirect()
                ->back()
                ->with('error', 'Er is geen depot gekoppeld aan je account. Contacteer een administrator.');
        }

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => [
                'nullable',
                Rule::in(self::TICKET_STATUSES),
            ],
        ]);

        $tickets = Ticket::with(['user', 'order', 'location'])
            ->where('location_id', $user->location_id)
            ->orderByRaw("
                CASE status
                    WHEN 'Open' THEN 1
                    WHEN 'In behandeling' THEN 2
                    WHEN 'Opgelost' THEN 3
                    ELSE 4
                END
            ")
            ->latest()
            ->get();

        if (! empty($validated['status'])) {
            $tickets = $tickets
                ->where('status', $validated['status'])
                ->values();
        }

        if (! empty($validated['search'])) {
            $search = $validated['search'];

            $tickets = $tickets
                ->filter(function (Ticket $ticket) use ($search) {
                    $searchableText = collect([
                        $ticket->subject,
                        $ticket->description,
                        $ticket->warehouse_note,
                        $ticket->status,
                        $ticket->user?->name,
                        $ticket->order_id,
                        $ticket->location?->province,
                        $ticket->location?->name,
                        $ticket->location?->city,
                    ])->filter()->implode(' ');

                    return FuzzySearch::matches($search, $searchableText);
                })
                ->values();
        }

        return view('tickets.warehouse.all', [
            'tickets' => $tickets,
            'ticketStatuses' => self::TICKET_STATUSES,
        ]);
    }

    public function showWarehouse(Ticket $ticket)
    {
        if ($ticket->location_id !== auth()->user()->location_id) {
            abort(403);
        }

        $ticket->load(['user', 'order', 'location']);

        return view('tickets.warehouse.show', [
            'ticket' => $ticket,
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
