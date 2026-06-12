<x-app-layout>
    <div class="p-8">
        <div class="mb-6">
            <x-page-header title="Supportaanvragen" />

            <p class="text-gray-600">
                Bekijk hier alle supportaanvragen van techniekers binnen jouw depot/provincie.
            </p>
        </div>

        @forelse ($tickets as $ticket)
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

                        <p class="text-sm text-gray-600">
                            Status: {{ $ticket->status }}
                        </p>

                        <p class="text-sm text-gray-600">
                            Aangemaakt op: {{ $ticket->created_at->format('d/m/Y') }}
                        </p>
                    </div>

                    <a href="{{ route('tickets.warehouse.show', $ticket) }}"
                       class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        Bekijken
                    </a>
                </div>
            </div>
        @empty
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-gray-600">
                    Er zijn nog geen supportaanvraag voor jouw depot/provincie.
                </p>
            </div>
        @endforelse
    </div>
</x-app-layout>
