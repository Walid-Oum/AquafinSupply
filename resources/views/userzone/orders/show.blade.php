{{--
    Pagina: Detail bestelling

    User Stories:
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
    <div class="p-4 md:p-8">
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <x-page-header title="Bestelling #{{ $order->id }}" />

                <p class="text-gray-600">
                    Bekijk hier de gegevens en materialen van je bestelling.
                </p>
            </div>

            <a
                href="{{ route('orders.index') }}"
                class="rounded-lg bg-gray-100 px-5 py-2.5 font-medium text-gray-700 transition hover:bg-gray-200"
            >
                Terug naar bestellingen
            </a>
        </div>

        <x-card>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <p class="text-sm text-gray-500">
                        Bestelling ID
                    </p>

                    <p class="font-semibold text-gray-900">
                        #{{ $order->id }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Status
                    </p>

                    <div class="mt-1">
                        <x-status-badge :status="$order->status" />
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Technieker
                    </p>

                    <p class="font-semibold text-gray-900">
                        {{ $order->user?->name ?? 'Onbekend' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Leverdatum
                    </p>

                    <p class="font-semibold text-gray-900">
                        {{ $order->delivery_date ?? 'Geen leverdatum' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Depot/provincie
                    </p>

                    <p class="font-semibold text-gray-900">
                        {{ $order->location?->province ?? 'Geen locatie gekoppeld' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Depot
                    </p>

                    <p class="font-semibold text-gray-900">
                        {{ $order->location?->name ?? 'Geen depot gekoppeld' }}
                    </p>
                </div>

                @if($order->location?->depot_address)
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">
                            Depotadres
                        </p>

                        <p class="font-semibold text-gray-900">
                            {{ $order->location->depot_address }}
                        </p>
                    </div>
                @endif

                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500">
                        Opmerking
                    </p>

                    <p class="font-semibold text-gray-900">
                        {{ $order->comment ?? 'Geen opmerking' }}
                    </p>
                </div>
            </div>
        </x-card>

        <div class="mt-6">
            <x-card>
                <h2 class="mb-4 text-xl font-semibold text-gray-900">
                    Bestelde materialen
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[620px]">
                        <thead>
                        <tr class="border-b text-sm text-gray-600">
                            <th class="p-3 text-left">
                                Afbeelding
                            </th>

                            <th class="p-3 text-left">
                                Materiaal
                            </th>

                            <th class="p-3 text-left">
                                Categorie
                            </th>

                            <th class="p-3 text-left">
                                Hoeveelheid
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($order->items as $item)
                            @php
                                $material = $item->material;
                                $fallbackImage = $categoryImages[$material->category] ?? 'sidebar-bg.jpg';

                                $imageUrl = $material->image
                                    ? asset('storage/' . $material->image)
                                    : asset('images/' . $fallbackImage);
                            @endphp

                            <tr class="border-b border-gray-100 last:border-0">
                                <td class="p-3">
                                    <img
                                        src="{{ $imageUrl }}"
                                        class="h-16 w-16 rounded-lg border object-cover"
                                        alt="{{ $material->name }}"
                                    >
                                </td>

                                <td class="p-3 font-medium text-gray-900">
                                    {{ $material->name }}
                                </td>

                                <td class="p-3 text-gray-700">
                                    {{ $material->category }}
                                </td>

                                <td class="p-3 font-semibold text-gray-900">
                                    {{ $item->quantity }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-gray-500 italic">
                                    Geen materialen gevonden.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
