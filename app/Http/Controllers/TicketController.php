<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserNotification;
use App\Support\FuzzySearch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Controller voor supportaanvragen.
 *
 * Deze controller beheert de ticketflow tussen techniekers en
 * magazijnmedewerkers.
 *
 * Techniekers kunnen:
 * - Eigen tickets bekijken
 * - Tickets zoeken via fuzzy search
 * - Nieuwe tickets aanmaken voor eigen bestellingen
 *
 * Magazijnmedewerkers kunnen:
 * - Tickets van hun eigen depot bekijken
 * - Tickets zoeken en filteren
 * - Ticketstatussen aanpassen
 * - Een magazijnnotitie toevoegen
 * - Notificaties versturen naar techniekers
 */
class TicketController extends Controller
{
    /**
     * Toegestane statussen voor tickets.
     */
    private const TICKET_STATUSES = [
        'Open',
        'In behandeling',
        'Opgelost',
    ];

    /**
     * Toon een overzicht van de tickets van de ingelogde technieker.
     *
     * Enkel tickets van de huidige gebruiker worden opgehaald.
     * Indien een zoekterm aanwezig is, wordt fuzzy search toegepast.
     *
     * @param Request $request De request met optionele zoekterm.
     * @return \Illuminate\View\View
     */
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

    /**
     * Toon het formulier om een nieuw ticket aan te maken.
     *
     * Enkel de eigen bestellingen van de ingelogde technieker
     * worden als mogelijke gekoppelde bestelling getoond.
     *
     * @return \Illuminate\View\View
     */
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

    /**
     * Sla een nieuw ticket op.
     *
     * Het ticket wordt gekoppeld aan de ingelogde technieker,
     * aan een eigen bestelling en aan het depot van die bestelling.
     * Na het aanmaken worden magazijnmedewerkers van hetzelfde depot
     * verwittigd via een notificatie.
     *
     * @param Request $request De request met bestelling, onderwerp en beschrijving.
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Toon alle tickets voor het depot van de magazijnmedewerker.
     *
     * De methode ondersteunt filteren op status en zoeken via fuzzy search.
     * Enkel tickets van hetzelfde depot als de magazijnmedewerker worden getoond.
     *
     * @param Request $request De request met optionele zoekterm en statusfilter.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
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

    /**
     * Geef zoeksuggesties terug voor tickets.
     *
     * Deze methode wordt gebruikt voor dynamische zoeksuggesties.
     * De resultaten houden rekening met de rol van de gebruiker:
     * magazijnmedewerkers zien tickets van hun depot, techniekers
     * zien enkel hun eigen tickets.
     *
     * @param Request $request De request met zoekterm.
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Toon de detailpagina van een ticket voor het magazijn.
     *
     * De methode controleert of het ticket bij hetzelfde depot hoort
     * als de ingelogde magazijnmedewerker.
     *
     * @param Ticket $ticket Het ticket dat geopend wordt.
     * @return \Illuminate\View\View
     */
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

    /**
     * Update de status en/of de magazijnnotitie van een ticket.
     *
     * Wanneer de status of magazijnnotitie wijzigt, wordt de technieker
     * automatisch op de hoogte gebracht via een notificatie.
     *
     * @param Ticket $ticket Het ticket dat aangepast wordt.
     * @param Request $request De request met nieuwe status en optionele magazijnnotitie.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Ticket $ticket, Request $request)
    {
        // Beveiliging: controleer locatie-overeenkomst
        if ($ticket->location_id !== auth()->user()->location_id) {
            abort(403);
        }

        // Valideer wijziging
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

        // Bewaar de oude waarden voor vergelijking
        $oldStatus = $ticket->status;
        $oldWarehouseNote = $ticket->warehouse_note;

        $newStatus = $validated['status'];
        $newWarehouseNote = $validated['warehouse_note'] ?? null;

        $ticket->update([
            'status' => $newStatus,
            'warehouse_note' => $newWarehouseNote,
        ]);

        // Controleer wat er veranderd is ten behoeve van de notificatietekst
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

    /**
     * Combineer alle doorzoekbare velden van een ticket tot één string.
     *
     * Dit zorgt ervoor dat FuzzySearch op alle relevante data kan zoeken,
     * zoals onderwerp, beschrijving, status, technieker, bestelling en locatie.
     *
     * @param Ticket $ticket Het ticket waarvoor de zoektekst wordt opgebouwd.
     * @return string De samengestelde zoektekst.
     */
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
