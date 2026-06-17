<x-app-layout>
    <div class="p-8">
        <div class="mb-6">
            <x-page-header title="Supportaanvragen" />

            <p class="text-gray-600">
                Bekijk hier alle supportaanvragen van techniekers binnen jouw depot/provincie.
            </p>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-800">
                {{ session('error') }}
            </div>
        @endif

       <div class="mb-6 flex justify-between items-center">

    <div class="relative w-96">

        <input
            id="ticket-search-input"
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Zoeken op onderwerp, technieker, bestelling..."
            autocomplete="off"
            class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
        >

        <ul
            id="ticket-search-results"
            class="absolute z-50 mt-1 hidden max-h-60 w-full divide-y divide-gray-100 overflow-y-auto rounded border border-gray-200 bg-white shadow-xl"
        ></ul>

    </div>

    <form
        id="ticket-search-form"
        method="GET"
        action="{{ route('tickets.warehouse.index') }}"
        class="flex gap-2">

        <select
            id="ticket-status-filter"
            name="status"
            class="border rounded px-3 py-2 text-sm">

            <option value="">
                Alle statussen
            </option>

            @foreach($ticketStatuses as $status)

                <option
                    value="{{ $status }}"
                    @selected(request('status') === $status)>

                    {{ $status }}

                </option>

            @endforeach

        </select>

        <x-button>
            Filter
        </x-button>

        <a
            href="{{ route('tickets.warehouse.index') }}"
            class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-400 transition">

            Reset

        </a>

    </form>

</div>

        @if($tickets->isEmpty())
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-gray-600">
                    Geen supportaanvragen gevonden.
                </p>

                @if(request('search'))
                    <p class="mt-2 text-sm text-gray-500">
                        Controleer je zoekterm of probeer een korter woord.
                    </p>
                @endif
            </div>
        @else
            @foreach($ticketStatuses as $status)
                @php
                    $statusTickets = $tickets->where('status', $status);
                @endphp

                @if($statusTickets->isNotEmpty())
                    <div class="mb-4 mt-6 flex items-center gap-3">
                        <h2 class="text-lg font-bold text-gray-900">
                            {{ $status }}
                        </h2>

                        <span class="rounded-full bg-gray-100 px-3 py-1 text-sm text-gray-600">
                            {{ $statusTickets->count() }}
                        </span>
                    </div>

                    @foreach ($statusTickets as $ticket)
                        <div class="mb-4 rounded-lg bg-white p-4 shadow">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h2 class="font-semibold text-gray-900">
                                        {{ $ticket->subject }}
                                    </h2>

                                    <p class="text-sm text-gray-600">
                                        Technieker: {{ $ticket->user->name ?? 'Onbekend' }}
                                    </p>

                                    <p class="text-sm text-gray-600">
                                        Bestelling: #{{ $ticket->order_id }}
                                    </p>

                                    <p class="text-sm text-gray-600">
                                        Depot/provincie:
                                        {{ $ticket->location->province ?? 'Geen provincie ingesteld' }}
                                    </p>

                                    <p class="text-sm text-gray-600">
                                        Depot:
                                        {{ $ticket->location->name ?? 'Geen depot gekoppeld' }}
                                    </p>

                                    <div class="mt-2">
                                        <x-status-badge :status="$ticket->status" />
                                    </div>

                                    @if($ticket->warehouse_note)
                                        <p class="mt-2 text-sm font-medium text-blue-700">
                                            Antwoord toegevoegd
                                        </p>
                                    @endif

                                    <p class="mt-1 text-sm text-gray-600">
                                        Aangemaakt op: {{ $ticket->created_at->format('d/m/Y') }}
                                    </p>
                                </div>

                                <a href="{{ route('tickets.warehouse.show', $ticket) }}">
                                    <x-button type="button">
                                        Bekijken
                                    </x-button>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            @endforeach
        @endif
    </div>

    <script>
        const ticketSearchInput = document.getElementById('ticket-search-input');
        const ticketResultsList = document.getElementById('ticket-search-results');
        const ticketSearchForm = document.getElementById('ticket-search-form');
        const ticketStatusFilter = document.getElementById('ticket-status-filter');

        let ticketSearchTimeout = null;

        ticketSearchInput.addEventListener('input', function () {
            const query = this.value.trim();

            clearTimeout(ticketSearchTimeout);

            if (query.length < 2) {
                ticketResultsList.innerHTML = '';
                ticketResultsList.classList.add('hidden');
                return;
            }

            ticketSearchTimeout = setTimeout(async function () {
                const response = await fetch(`/api/search-tickets?q=${encodeURIComponent(query)}`);
                const tickets = await response.json();

                ticketResultsList.innerHTML = '';

                if (tickets.length === 0) {
                    ticketResultsList.classList.add('hidden');
                    return;
                }

                tickets.forEach(function (ticket) {
                    const item = document.createElement('li');

                    item.className = 'cursor-pointer px-4 py-3 hover:bg-gray-100';

                    item.innerHTML = `
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-gray-800">${ticket.subject}</p>
                                <p class="text-sm text-gray-500">${ticket.technician} — ${ticket.order}</p>
                            </div>

                            <span class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-600">
                                ${ticket.status}
                            </span>
                        </div>
                    `;

                    item.addEventListener('click', function () {
                        ticketSearchInput.value = ticket.subject;
                        ticketResultsList.classList.add('hidden');
                        ticketSearchForm.submit();
                    });

                    ticketResultsList.appendChild(item);
                });

                ticketResultsList.classList.remove('hidden');
            }, 250);
        });

        ticketStatusFilter.addEventListener('change', function () {
            ticketSearchForm.submit();
        });

        document.addEventListener('click', function (event) {
            if (
                !ticketSearchInput.contains(event.target) &&
                !ticketResultsList.contains(event.target)
            ) {
                ticketResultsList.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
