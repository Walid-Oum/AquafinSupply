{{-- 
    Pagina: Overzicht bestellingen

    User Stories:
    US12 - Eigen bestellingen bekijken
    US15 - Alle bestellingen bekijken
--}}
<x-app-layout>

    <x-page-header title="Bestellingen"/>

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

<tr>

    <td class="p-3">
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

                            <x-status-badge
                                :status="$order->status" />

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

    <td colspan="5" class="text-center p-4">

        Geen bestellingen gevonden.

    </td>

</tr>

@endforelse

</tbody>
        </table>

    </x-card>

</x-app-layout>