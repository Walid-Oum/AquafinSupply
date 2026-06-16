<x-app-layout>
    <div class="p-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <x-page-header title="Mijn supportaanvragen" />

                <p class="text-gray-600">
                    Bekijk hier de status van je supportaanvragen.
                </p>
            </div>

            <a href="{{ route('tickets.create') }}">
                <x-button>
                    Nieuwe supportaanvraag
                </x-button>
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @forelse ($tickets as $ticket)
            <div class="mb-4 rounded-lg bg-white p-4 shadow">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="font-semibold text-gray-900">
                            {{ $ticket->subject }}
                        </h2>

                        <div class="mt-2">
                            <x-status-badge :status="$ticket->status" />
                        </div>

                        <p class="mt-2 text-sm text-gray-600">
                            Aangemaakt op: {{ $ticket->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                @if($ticket->warehouse_note)
                    <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-gray-700">
                        <p class="font-semibold text-blue-700">
                            Antwoord van magazijn:
                        </p>

                        <p class="mt-1">
                            {{ $ticket->warehouse_note }}
                        </p>
                    </div>
                @else
                    <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm text-gray-500">
                        Nog geen antwoord van het magazijn.
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-gray-600">
                    Je hebt nog geen supportaanvraag aangemaakt.
                </p>
            </div>
        @endforelse
    </div>
</x-app-layout> 
