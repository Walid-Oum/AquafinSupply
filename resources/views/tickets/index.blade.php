<x-app-layout>
    <div class="p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Mijn tickets</h1>
            <p class="text-gray-600">Bekijk hier de status van je aangemaakte tickets.</p>
        </div>

        @forelse ($tickets as $ticket)
            <div class="mb-4 rounded-lg bg-white p-4 shadow">
                <h2 class="font-semibold text-gray-900">{{ $ticket->subject }}</h2>
                <p class="text-sm text-gray-600">Status: {{ $ticket->status }}</p>
                <p class="text-sm text-gray-600">Aangemaakt op: {{ $ticket->created_at->format('d/m/Y') }}</p>
            </div>
        @empty
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-gray-600">Je hebt nog geen tickets.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>




