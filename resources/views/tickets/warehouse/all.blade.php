{{--
    MAGAZIJN - ALLE SUPPORTAANVRAGEN (TICKETS)

    @author      Walid
    @version     1.0
    @since       2026-06-18

    Deze view toont alle supportaanvragen van techniekers voor
    magazijnmedewerkers. Magazijnmedewerkers kunnen tickets bekijken,
    filteren op status, zoeken op onderwerp, technieker of bestelling.
    Tickets worden gegroepeerd per status en per provincie/depot.

    @see App\Http\Controllers\TicketController::all()
    @see App\Http\Controllers\TicketController::showWarehouse()

    @uses App\View\Components\AppLayout
    @uses App\View\Components\PageHeader
    @uses App\View\Components\StatusBadge
--}}

<x-app-layout>
    <div class="space-y-6">
        {{-- HEADER --}}
        <div>
            <x-page-header title="Supportaanvragen" />

            <p class="mt-1 text-sm text-gray-600 sm:text-base">
                Bekijk hier alle supportaanvragen van techniekers binnen jouw depot/provincie.
            </p>
        </div>

        {{-- SUCCES & FOUTMELDINGEN --}}
        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        {{-- FILTERS EN ZOEKBALK --}}
        <section class="rounded-2xl border border-gray-100 bg-white p-3 shadow-sm sm:p-4">
            <form
                id="ticket-search-form"
                method="GET"
                action="{{ route('tickets.warehouse.index') }}"
                class="grid grid-cols-1 gap-3 lg:grid-cols-4 lg:items-start"
            >
                <div class="relative lg:col-span-2">
                    <input
                        id="ticket-search-input"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Zoeken op onderwerp, technieker, bestelling..."
                        autocomplete="off"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                    >

                    <ul
                        id="ticket-search-results"
                        class="absolute z-50 mt-2 hidden max-h-60 w-full divide-y divide-gray-100 overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-xl"
                    ></ul>
                </div>

                <select
                    id="ticket-status-filter"
                    name="status"
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                >
                    <option value="">
                        Alle statussen
                    </option>

                    @foreach($ticketStatuses as $status)
                        <option
                            value="{{ $status }}"
                            @selected(request('status') === $status)
                        >
                            {{ $status }}
                        </option>
                    @endforeach
                </select>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-2">
                    <x-button type="submit" class="w-full justify-center">
                        Filter
                    </x-button>

                    <a
                        href="{{ route('tickets.warehouse.index') }}"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200"
                    >
                        Reset
                    </a>
                </div>
            </form>
        </section>

        {{-- TICKETS PER STATUS --}}
        @if($tickets->isEmpty())
            <section class="rounded-2xl border border-gray-100 bg-white p-6 text-center shadow-sm">
                <p class="text-gray-600 italic">
                    Geen supportaanvragen gevonden.
                </p>

                @if(request('search'))
                    <p class="mt-2 text-sm text-gray-500">
                        Controleer je zoekterm of probeer een korter woord.
                    </p>
                @endif
            </section>
        @else
            <section class="space-y-6">
                @foreach($ticketStatuses as $status)
                    @php
                        $statusTickets = $tickets->where('status', $status);
                    @endphp

                    @if($statusTickets->isNotEmpty())
                        <div>
                            <div class="mb-3 flex items-center gap-3">
                                <h2 class="text-lg font-bold text-[#0F4C81]">
                                    {{ $status }}
                                </h2>

                                <span class="rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-600">
                                    {{ $statusTickets->count() }}
                                </span>
                            </div>

                            <div class="space-y-3">
                                @foreach ($statusTickets as $ticket)
                                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition hover:shadow-md sm:p-5">
                                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                            <div class="min-w-0 flex-1">
                                                <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                    <div class="min-w-0">
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                                            Supportaanvraag
                                                        </p>

                                                        <h3 class="mt-1 text-lg font-bold leading-snug text-[#0F4C81] sm:text-xl">
                                                            {{ $ticket->subject }}
                                                        </h3>
                                                    </div>

                                                    <div class="shrink-0">
                                                        <x-status-badge :status="$ticket->status" />
                                                    </div>
                                                </div>

                                                {{-- TICKET DETAILS --}}
                                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                                                    <div class="rounded-xl bg-gray-50 p-3">
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                                            Technieker
                                                        </p>

                                                        <p class="mt-1 font-semibold text-gray-900">
                                                            {{ $ticket->user?->name ?? 'Onbekend' }}
                                                        </p>
                                                    </div>

                                                    <div class="rounded-xl bg-gray-50 p-3">
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                                            Bestelling
                                                        </p>

                                                        <p class="mt-1 font-semibold text-gray-900">
                                                            #{{ $ticket->order_id }}
                                                        </p>
                                                    </div>

                                                    <div class="rounded-xl bg-gray-50 p-3">
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                                            Depot/provincie
                                                        </p>

                                                        <p class="mt-1 font-semibold text-gray-900">
                                                            {{ $ticket->location?->province ?? 'Geen provincie ingesteld' }}
                                                        </p>

                                                        <p class="mt-1 text-sm text-gray-500">
                                                            {{ $ticket->location?->name ?? 'Geen depot gekoppeld' }}
                                                        </p>
                                                    </div>

                                                    <div class="rounded-xl bg-gray-50 p-3">
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                                            Aangemaakt op
                                                        </p>

                                                        <p class="mt-1 font-semibold text-gray-900">
                                                            {{ $ticket->created_at->format('d/m/Y') }}
                                                        </p>
                                                    </div>
                                                </div>

                                                {{-- WAREHOUSE NOTE STATUS --}}
                                                @if($ticket->warehouse_note)
                                                    <div class="mt-4 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                                                        <p class="font-semibold">
                                                            Antwoord toegevoegd
                                                        </p>

                                                        <p class="mt-1 text-blue-700">
                                                            Er is al een antwoord van het magazijn toegevoegd.
                                                        </p>
                                                    </div>
                                                @else
                                                    <div class="mt-4 rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                                                        <p class="font-semibold">
                                                            Nog geen antwoord
                                                        </p>

                                                        <p class="mt-1">
                                                            Deze supportaanvraag moet nog opgevolgd worden.
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="lg:w-40">
                                                <a
                                                    href="{{ route('tickets.warehouse.show', $ticket) }}"
                                                    class="block"
                                                >
                                                    <x-button type="button" class="w-full justify-center">
                                                        Bekijken
                                                    </x-button>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </section>
        @endif
    </div>

    {{-- ZOEKFUNCTIE VOOR TICKETS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ticketSearchInput = document.getElementById('ticket-search-input');
            const ticketResultsList = document.getElementById('ticket-search-results');
            const ticketSearchForm = document.getElementById('ticket-search-form');
            const ticketStatusFilter = document.getElementById('ticket-status-filter');

            let ticketSearchTimeout = null;

            function getTicketSubject(ticket) {
                return ticket.subject
                    ?? ticket.title
                    ?? ticket.name
                    ?? ticket.label
                    ?? `Supportaanvraag #${ticket.id ?? ''}`;
            }

            function getTicketTechnician(ticket) {
                return ticket.technician
                    ?? ticket.user_name
                    ?? ticket.user?.name
                    ?? ticket.user
                    ?? 'Onbekende technieker';
            }

            function getTicketOrder(ticket) {
                if (ticket.order) {
                    return ticket.order;
                }

                if (ticket.order_id) {
                    return `Bestelling #${ticket.order_id}`;
                }

                if (ticket.order?.id) {
                    return `Bestelling #${ticket.order.id}`;
                }

                return 'Geen bestelling';
            }

            function getTicketStatus(ticket) {
                return ticket.status
                    ?? ticket.ticket_status
                    ?? 'Onbekend';
            }

            function clearTicketResults() {
                ticketResultsList.innerHTML = '';
                ticketResultsList.classList.add('hidden');
            }

            if (ticketSearchInput && ticketResultsList && ticketSearchForm) {
                ticketSearchInput.addEventListener('input', function () {
                    const query = this.value.trim();

                    clearTimeout(ticketSearchTimeout);

                    if (query.length < 2) {
                        clearTicketResults();
                        return;
                    }

                    ticketSearchTimeout = setTimeout(async function () {
                        try {
                            const response = await fetch(`/api/search-tickets?q=${encodeURIComponent(query)}`);

                            if (! response.ok) {
                                clearTicketResults();
                                return;
                            }

                            const tickets = await response.json();

                            ticketResultsList.innerHTML = '';

                            if (!Array.isArray(tickets) || tickets.length === 0) {
                                clearTicketResults();
                                return;
                            }

                            tickets.forEach(function (ticket) {
                                const subject = getTicketSubject(ticket);
                                const technician = getTicketTechnician(ticket);
                                const order = getTicketOrder(ticket);
                                const status = getTicketStatus(ticket);

                                const item = document.createElement('li');

                                item.className = 'cursor-pointer px-4 py-3 transition hover:bg-gray-50';

                                const wrapper = document.createElement('div');
                                wrapper.className = 'flex items-center justify-between gap-3';

                                const textWrapper = document.createElement('div');
                                textWrapper.className = 'min-w-0';

                                const subjectElement = document.createElement('p');
                                subjectElement.className = 'font-semibold text-gray-800';
                                subjectElement.textContent = subject;

                                const metaElement = document.createElement('p');
                                metaElement.className = 'text-sm text-gray-500';
                                metaElement.textContent = `${technician} — ${order}`;

                                const statusElement = document.createElement('span');
                                statusElement.className = 'shrink-0 rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600';
                                statusElement.textContent = status;

                                textWrapper.appendChild(subjectElement);
                                textWrapper.appendChild(metaElement);

                                wrapper.appendChild(textWrapper);
                                wrapper.appendChild(statusElement);

                                item.appendChild(wrapper);

                                item.addEventListener('click', function () {
                                    ticketSearchInput.value = subject;
                                    ticketResultsList.classList.add('hidden');
                                    ticketSearchForm.submit();
                                });

                                ticketResultsList.appendChild(item);
                            });

                            ticketResultsList.classList.remove('hidden');
                        } catch (error) {
                            clearTicketResults();
                        }
                    }, 250);
                });
            }

            if (ticketStatusFilter && ticketSearchForm) {
                ticketStatusFilter.addEventListener('change', function () {
                    ticketSearchForm.submit();
                });
            }

            document.addEventListener('click', function (event) {
                if (
                    ticketSearchInput &&
                    ticketResultsList &&
                    !ticketSearchInput.contains(event.target) &&
                    !ticketResultsList.contains(event.target)
                ) {
                    ticketResultsList.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>