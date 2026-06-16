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

        <form
            method="GET"
            action="{{ route('tickets.warehouse.index') }}"
            class="mb-6 flex flex-wrap items-center gap-3 rounded-lg bg-white p-4 shadow"
        >
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Zoeken op onderwerp, technieker, bestelling..."
                class="w-full rounded-lg border border-gray-300 px-4 py-2 md:w-80"
            >

            <select
                name="status"
                class="rounded-lg border border-gray-300 px-4 py-2"
            >
                <option value="">
                    Alle statussen
                </option>

                @foreach($ticketStatuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>
                        {{ $status }}
                    </option>
                @endforeach
            </select>

            <x-button type="submit">
                Zoeken
            </x-button>

            <a
                href="{{ route('tickets.warehouse.index') }}"
                class="rounded-lg bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300"
            >
                Reset
            </a>
        </form>

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

                                <a
                                    href="{{ route('tickets.warehouse.show', $ticket) }}"
                                    class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                                >
                                    Bekijken
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            @endforeach
        @endif
    </div>
</x-app-layout>
