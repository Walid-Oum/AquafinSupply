@php
    $localStock = $material->stocks->first();

    $stock = $localStock?->stock ?? 0;
    $minimumStock = $localStock?->minimum_stock ?? $material->minimum_stock ?? 0;
@endphp

<x-app-layout>

    <x-page-header title="Materiaal details" />

    <x-card>

        @if($material->image)
            <img
                src="{{ asset('storage/' . $material->image) }}"
                class="w-64 rounded-lg mb-6"
                alt="{{ $material->name }}"
            >
        @else
            <div class="w-64 h-40 rounded-lg mb-6 bg-gray-100 flex items-center justify-center text-gray-500">
                Geen afbeelding
            </div>
        @endif

        <div class="space-y-3">

            <p>
                <strong>Naam:</strong>
                {{ $material->name }}
            </p>

            <p>
                <strong>Categorie:</strong>
                {{ $material->category }}
            </p>

            <p>
                <strong>Beschrijving:</strong>
                {{ $material->description }}
            </p>

            <p>
                <strong>Voorraad in jouw depot:</strong>
                {{ $stock }}
            </p>

            <p>
                <strong>Minimumvoorraad in jouw depot:</strong>
                {{ $minimumStock }}
            </p>

            <p>
                <strong>Voorraadstatus:</strong>

                @if($stock <= 0)
                    <span class="inline-block bg-red-100 text-red-700 text-xs font-semibold px-3 py-1 rounded-full">
                        Geen voorraad
                    </span>
                @elseif($stock <= $minimumStock)
                    <span class="inline-block bg-orange-100 text-orange-700 text-xs font-semibold px-3 py-1 rounded-full">
                        Lage voorraad
                    </span>
                @else
                    <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                        OK
                    </span>
                @endif
            </p>

        </div>

        <div class="mt-6">
            <a href="{{ route('magazijn.materials.index') }}">
                <x-button>
                    Terug
                </x-button>
            </a>
        </div>

    </x-card>

</x-app-layout>
