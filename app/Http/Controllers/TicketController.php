<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserNotification;
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

    public function index(Request $request)
    {
        $tickets = Ticket::with(['order', 'location'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        if ($request->filled('search')) {
            $search = $request->search;

            $tickets = $tickets
                ->filter(function (Ticket $ticket) use ($search) {
                    return FuzzySearch::matches($search, $this->ticketSearchText($ticket));
                })
                ->values();
        }

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

        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'location_id' => $order->location_id,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'Open',
        ]);

        $warehouseUsers = User::where('role', 'magazijn')
            ->where('location_id', $ticket->location_id)
            ->get();

        foreach ($warehouseUsers as $warehouseUser) {
            UserNotification::create([
                'user_id' => $warehouseUser->id,
                'title' => 'Nieuwe supportaanvraag',
                'message' => 'Er is een nieuwe supportaanvraag "' . $ticket->subject . '" aangemaakt.',
                'link' => route('tickets.warehouse.show', $ticket),
            ]);
        }

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
                    return FuzzySearch::matches($search, $this->ticketSearchText($ticket));
                })
                ->values();
        }

        return view('tickets.warehouse.all', [
            'tickets' => $tickets,
            'ticketStatuses' => self::TICKET_STATUSES,
        ]);
    }

    public function searchSuggestions(Request $request)
    {
        $search = trim((string) $request->get('q', ''));

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $user = auth()->user();

        if (! $user) {
            return response()->json([]);
        }

        $tickets = Ticket::with(['user', 'order', 'location']);

        if ($user->role === 'magazijn') {
            if (! $user->location_id) {
                return response()->json([]);
            }

            $tickets->where('location_id', $user->location_id);
        } else {
            $tickets->where('user_id', $user->id);
        }

        $tickets = $tickets
            ->latest()
            ->get()
            ->filter(function (Ticket $ticket) use ($search) {
                return FuzzySearch::matches($search, $this->ticketSearchText($ticket));
            })
            ->take(5)
            ->map(function (Ticket $ticket) use ($user) {
                $subtitleParts = collect([
                    $ticket->user?->name,
                    'Bestelling #' . $ticket->order_id,
                ])->filter()->implode(' — ');

                return [
                    'label' => $ticket->subject,
                    'subtitle' => $subtitleParts,
                    'badge' => $ticket->status,
                    'url' => $user->role === 'magazijn'
                        ? route('tickets.warehouse.show', $ticket)
                        : null,
                ];
            })
            ->values();

        return response()->json($tickets);
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

        $oldStatus = $ticket->status;
        $oldWarehouseNote = $ticket->warehouse_note;

        $newStatus = $validated['status'];
        $newWarehouseNote = $validated['warehouse_note'] ?? null;

        $ticket->update([
            'status' => $newStatus,
            'warehouse_note' => $newWarehouseNote,
        ]);

        $statusChanged = $oldStatus !== $newStatus;
        $noteChanged = $oldWarehouseNote !== $newWarehouseNote && ! empty($newWarehouseNote);

        if ($statusChanged || $noteChanged) {
            if ($statusChanged && $noteChanged) {
                $message = 'Je supportaanvraag "' . $ticket->subject . '" kreeg status "' . $newStatus . '" en een antwoord van het magazijn.';
            } elseif ($statusChanged) {
                $message = 'Je supportaanvraag "' . $ticket->subject . '" kreeg status "' . $newStatus . '".';
            } else {
                $message = 'Je supportaanvraag "' . $ticket->subject . '" kreeg een antwoord van het magazijn.';
            }

            UserNotification::create([
                'user_id' => $ticket->user_id,
                'title' => 'Supportaanvraag bijgewerkt',
                'message' => $message,
                'link' => route('tickets.index'),
            ]);
        }

        return redirect()
            ->route('tickets.warehouse.show', $ticket)
            ->with('success', 'Ticket succesvol bijgewerkt.');
    }

    private function ticketSearchText(Ticket $ticket): string
    {
        return collect([
            $ticket->subject,
            $ticket->description,
            $ticket->warehouse_note,
            $ticket->status,
            $ticket->user?->name,
            $ticket->order_id,
            'Bestelling #' . $ticket->order_id,
            $ticket->location?->province,
            $ticket->location?->name,
            $ticket->location?->city,
        ])->filter()->implode(' ');
    }
}
