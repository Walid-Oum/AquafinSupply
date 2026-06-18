{{--

Pagina: Materiaal detail

Beschrijving:
Toont de detailinformatie van een specifiek materiaal en de voorraadverdeling over verschillende depots.

Functionaliteiten:
- Weergeven van materiaalgegevens
- Tonen van gekoppelde risiconiveaus
- Weergeven van materiaalafbeeldingen
- Overzicht van voorraad per depot
- Signaleren van lage voorraadniveaus
- Navigeren naar de bewerkpagina van het materiaal

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
    <x-page-header title="Materiaal detail" />

    <x-card>
        <div class="mb-4">
            <strong>Afbeelding:</strong><br>
           @if($material->image)
    <img
        src="{{ Storage::url($material->image) }}"
        class="w-full max-w-sm h-auto object-cover rounded mt-2">
@else
    <img
        src="{{ asset('images/' . ($categoryImages[$material->category] ?? 'sidebar-bg.jpg')) }}"
        class="w-full max-w-sm h-auto object-cover rounded mt-2"
        alt="{{ $material->category }}">
@endif
        </div>

        <div class="mb-4">
            <strong>Naam:</strong> {{ $material->name }}
        </div>
        <div class="mb-4">
            <strong>Categorie:</strong> {{ $material->category }}
        </div>
        <div class="mb-4">
    <strong>Risiconiveau:</strong>

    <div class="mt-2 flex gap-2 flex-wrap">

        @forelse($material->riskLevels as $riskLevel)

            <span
                class="inline-block px-3 py-1 rounded-full text-xs font-semibold

                @if($riskLevel->name === 'Hoog')
                    bg-red-100 text-red-700
                @elseif($riskLevel->name === 'Gemiddeld')
                    bg-yellow-100 text-yellow-700
                @else
                    bg-green-100 text-green-700
                @endif">

                {{ $riskLevel->name }}

            </span>

        @empty

            <span class="text-gray-500">
                Geen risiconiveau gekoppeld
            </span>

        @endforelse

    </div>
</div>
        <div class="mb-4">
            <strong>Beschrijving:</strong> {{ $material->description ?? 'Geen beschrijving' }}
        </div>

        <div class="mb-4">
            <strong>Status:</strong>
            @if($material->is_active)
                <span class="text-green-600">Actief</span>
            @else
                <span class="text-red-600">Inactief</span>
            @endif
        </div>

<hr class="my-6">

<h3 class="text-xl font-bold mb-4">
    Voorraad per depot
</h3>

<div class="hidden lg:block overflow-x-auto">

    <x-table>

    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-3 text-left">
                Depot
            </th>

            <th class="px-4 py-3 text-left">
                Voorraad
            </th>

            <th class="px-4 py-3 text-left">
                Minimum voorraad
            </th>

            <th class="px-4 py-3 text-left">
                Status
            </th>
        </tr>
    </thead>

    <tbody>

        @foreach($material->stocks as $stock)

            <tr class="border-t">

                <td class="px-4 py-3">
                    {{ $stock->location->name }}
                </td>

                <td class="px-4 py-3">
                    {{ $stock->stock }}
                </td>

                <td class="px-4 py-3">
                    {{ $stock->minimum_stock }}
                </td>

                <td class="px-4 py-3">

                    @if($stock->stock <= $stock->minimum_stock)

                        <span class="inline-block bg-red-100 text-red-700 text-xs font-semibold px-3 py-1 rounded-full">
                            Lage voorraad
                        </span>

                    @else

                        <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                            OK
                        </span>

                    @endif

                </td>

            </tr>

        @endforeach

    </tbody>

    </x-table>

</div>

<div class="lg:hidden space-y-4 mb-6">

    @foreach($material->stocks as $stock)

        <x-card>

            <p>
                <strong>Depot:</strong>
                {{ $stock->location->name }}
            </p>

            <p>
                <strong>Voorraad:</strong>
                {{ $stock->stock }}
            </p>

            <p>
                <strong>Minimum voorraad:</strong>
                {{ $stock->minimum_stock }}
            </p>

            <div class="mt-2">

                @if($stock->stock <= $stock->minimum_stock)

                    <span class="inline-block bg-red-100 text-red-700 text-xs font-semibold px-3 py-1 rounded-full">
                        Lage voorraad
                    </span>

                @else

                    <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                        OK
                    </span>

                @endif

            </div>

        </x-card>

    @endforeach

</div>

      <div class="flex justify-end gap-4">
            <a href="{{ route('materials.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Terug</a>
         <a href="{{ route('materials.edit', $material->id) }}">
        <x-button>
            Bewerk
        </x-button>
    </a>
        </div>
    </x-card>
</x-app-layout>
