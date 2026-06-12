<x-app-layout>
    <div class="p-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <x-page-header title="Mijn supportaanvragen" />
                <p class="text-gray-600">Bekijk hier de status van je supportaanvragen</p>
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
                <h2 class="font-semibold text-gray-900">{{ $ticket->subject }}</h2>
                <p class="text-sm text-gray-600">Status: {{ $ticket->status }}</p>
                <p class="text-sm text-gray-600">Aangemaakt op: {{ $ticket->created_at->format('d/m/Y') }}</p>
            </div>
        @empty
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-gray-600">Je hebt nog geen supportaanvraag aangemaakt.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>




