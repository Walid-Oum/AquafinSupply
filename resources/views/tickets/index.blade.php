<x-app-layout>
    <div class="p-8">
        <div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <x-page-header title="Mijn tickets" />
                <p class="text-gray-600">Bekijk hier de status van je aangemaakte tickets.</p>
            </div>

            <div class="flex flex-wrap gap-4 items-center w-full md:w-auto justify-end">
                <form method="GET" action="{{ route('tickets.index') }}" class="flex gap-2 items-center">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Zoek op onderwerp of status..." 
                        class="border rounded px-3 py-2 w-64 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F4C81] text-black">
                    
                    <button type="submit" class="bg-[#0F4C81] hover:bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium transition">
                        Zoek
                    </button>

                    @if(request('search'))
                        <a href="{{ route('tickets.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-400 transition">
                            Wis
                        </a>
                    @endif
                </form>

                <a href="{{ route('tickets.create') }}">
                    <x-button>
                        Nieuw ticket
                    </x-button>
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @forelse ($tickets as $ticket)
            <div class="mb-4 rounded-lg bg-white p-4 shadow hover:bg-gray-50 transition">
                <h2 class="font-semibold text-gray-900">{{ $ticket->subject }}</h2>
                <p class="text-sm text-gray-600">
                    Status: 
                    <span class="font-medium {{ $ticket->status == 'Open' ? 'text-green-600' : 'text-orange-500' }}">
                        {{ $ticket->status }}
                    </span>
                </p>
                <p class="text-sm text-gray-600">Aangemaakt op: {{ $ticket->created_at->format('d/m/Y') }}</p>
            </div>
        @empty
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-gray-600 italic">Geen tickets gevonden.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>