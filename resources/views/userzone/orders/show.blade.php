{{-- 
    Pagina: Detail bestelling

    User Stories:
    US17 - Inhoud bestelling bekijken
--}}
{{-- 
US17 - Inhoud bestelling bekijken
--}}

<x-app-layout>

    <x-page-header title="Bestelling Detail"/>

    <x-card>

        <div class="space-y-4">

            <div>
                <strong>Bestelling ID:</strong> #{{ $order->id }}
            </div>

            <div>
                <strong>Technieker:</strong> {{ $order->user->name }}
            </div>

            <div>
                <strong>Leverdatum:</strong> {{ $order->delivery_date }}
            </div>

            <div>
                <strong>Status:</strong>

                <x-status-badge :status="$order->status"/>
            </div>

            <div>
                <strong>Opmerking:</strong>

{{ $order->comment ?? 'Geen opmerking' }}
            </div>

        </div>

    </x-card>

    <div class="mt-6">

        <x-card>

            <h2 class="text-xl font-semibold mb-4">

                Bestelde materialen

            </h2>

            <table class="w-full">

                <thead>

                    <tr class="border-b">

                        <th class="text-left p-3">
                            Materiaal
                        </th>

                        <th class="text-left p-3">
                            Hoeveelheid
                        </th>

                    </tr>

                </thead>

               <tbody>

@foreach($order->items as $item)

<tr>

    <td class="p-3">

        {{ $item->material->name }}

    </td>

    <td class="p-3">

        {{ $item->quantity }}

    </td>

</tr>

@endforeach

</tbody>

            </table>

        </x-card>

    </div>

</x-app-layout>