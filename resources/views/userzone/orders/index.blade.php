{{-- 
    Pagina: Overzicht bestellingen

    User Stories:
    US12 - Eigen bestellingen bekijken
    US15 - Alle bestellingen bekijken
--}}
<x-app-layout>

    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
        <x-page-header title="Bestellingen"/>

        <form method="GET" action="{{ url()->current() }}" class="flex gap-2 items-center w-full md:w-auto justify-end">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}"
                placeholder="Zoek op ID, technieker of status..." 
                class="border rounded px-3 py-2 w-64 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F4C81] text-black">
            
            <button type="submit" class="bg-[#0F4C81] hover:bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium transition">
                Zoek
            </button>

            @if(request('search'))
                <a href="{{ url()->current() }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-400 transition">
                    Wis
                </a>
            @endif
        </form>
    </div>

    <x-card>

        <table class="w-full">

            <thead>

                <tr class="border-b">

                    <th class="p-3 text-left">
                        ID
                    </th>

                    <th class="p-3 text-left">
                        Technieker
                    </th>
                    
                    <th class="p-3 text-left">
                        Besteld op
                    </th>
                    
                    <th class="p-3 text-left">
                        Leverdatum
                    </th>

                    <th class="p-3 text-left">
                        Status
                    </th>

                    <th class="p-3 text-left">
                        Actie
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($orders as $order)

                <tr class="hover:bg-gray-50 transition border-b border-gray-100 last:border-0">

                    <td class="p-3 font-medium text-gray-900">
                        #{{ $order->id }}
                    </td>

                    <td class="p-3">
                        {{ $order->user->name }}
                    </td>
                    
                    <td class="p-3">
                        {{ $order->created_at->format('d/m/Y') }}
                    </td>

                    <td class="p-3">
                        {{ $order->delivery_date }}
                    </td>

                    <td class="p-3">
                        <x-status-badge :status="$order->status" />
                    </td>

                    <td class="p-3">
                        <a href="{{ route('orders.show', $order->id) }}">
                            <x-button>
                                Bekijken
                            </x-button>
                        </a>
                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="6" class="text-center p-4 text-gray-500 italic">
                        Geen bestellingen gevonden.
                    </td>
                </tr>

                @endforelse

            </tbody>
        </table>

    </x-card>

</x-app-layout>