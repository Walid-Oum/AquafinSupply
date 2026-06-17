<x-app-layout>
    <div class="p-8">
        <div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <x-page-header title="Mijn supportaanvragen" />

                <p class="text-gray-600">
                    Bekijk hier de status van je supportaanvragen.
                </p>
            </div>

<<<<<<< HEAD
            <a href="{{ route('tickets.create') }}">
                <x-button>
                    Nieuwe supportaanvraag
                </x-button>
            </a>
=======
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
                        Nieuwe supportaanvraag
                    </x-button>
                </a>
            </div>
>>>>>>> 37120beb3a1b1f6b8f864c9da4bd4edc63aa9be7
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @forelse ($tickets as $ticket)
<<<<<<< HEAD
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
=======
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
                <p class="text-gray-600 italic">Geen supportaanvraag aangemaakt gevonden.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
>>>>>>> 37120beb3a1b1f6b8f864c9da4bd4edc63aa9be7
