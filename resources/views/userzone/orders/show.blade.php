{{--
    Pagina: Detail bestelling

    User Stories:
    US17 - Inhoud bestelling bekijken
--}}
{{--
US17 - Inhoud bestelling bekijken
--}}
@php
    $categoryImages = [
        'Aquafin tools' => 'aquafintools.png',
        'Bevestigingsmateriaal' => 'bevestigingsmateriaal.png',
        'Gereedschap' => 'gereedschap.png',
        'PBM' => 'PBM.png',
        'Technisch onderhoud' => 'technischeonderhoud.png',
        'Verbruiksgoederen' => 'verbruiksgoederen.png',
    ];
@endphp

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
                <strong>Depot/provincie:</strong>
                {{ $order->location->province ?? 'Geen locatie gekoppeld' }}
            </div>

            <div>
                <strong>Depot:</strong>
                {{ $order->location->name ?? 'Geen depot gekoppeld' }}
            </div>

            @if($order->location?->depot_address)
                <div>
                    <strong>Depotadres:</strong>
                    {{ $order->location->depot_address }}
                </div>
            @endif

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
            Afbeelding
        </th>

        <th class="text-left p-3">
            Materiaal
        </th>

        <th class="text-left p-3">
            Hoeveelheid
        </th>

    </tr>

</thead>

<tbody>

    @forelse($order->items as $item)

        <tr>

            <td class="p-3">

            @if($item->material->image)

    <img
        src="{{ asset('storage/' . $item->material->image) }}"
        class="w-16 h-16 object-cover rounded-lg border">

@else

    <img
        src="{{ asset('images/' . ($categoryImages[$item->material->category] ?? 'sidebar-bg.jpg')) }}"
        class="w-16 h-16 object-cover rounded-lg border"
        alt="{{ $item->material->category }}">

@endif

            </td>

            <td class="p-3">

                {{ $item->material->name }}

            </td>

            <td class="p-3">

                {{ $item->quantity }}

            </td>

        </tr>

    @empty

        <tr>

            <td colspan="3" class="text-center p-4 text-gray-500">

                Geen materialen gevonden.

            </td>

        </tr>

    @endforelse

</tbody>

            </table>

        </x-card>

    </div>

</x-app-layout>
