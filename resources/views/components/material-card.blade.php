@props([
    'material',
    'compact' => false,
])


@php
    $localStock = $material->stocks->first();
    $stock = $localStock?->stock ?? 0;
    $minimumStock = $localStock?->minimum_stock ?? 0;

    $isOutOfStock = $stock <= 0;
    $isLowStock = !$isOutOfStock && $stock <= $minimumStock;

    $categoryImages = [
        'Aquafin tools' => 'aquafintools.png',
        'Bevestigingsmateriaal' => 'bevestigingsmateriaal.png',
        'Gereedschap' => 'gereedschap.png',
        'PBM' => 'PBM.png',
        'Technisch onderhoud' => 'technischeonderhoud.png',
        'Verbruiksgoederen' => 'verbruiksgoederen.png',
    ];

    $image = $categoryImages[$material->category] ?? 'sidebar-bg.jpg';
@endphp

@if($compact)
    <div class="border rounded-lg p-4 bg-white hover:shadow transition block">
        <a href="{{ route('technician.materials.show', $material->id) }}" class="block">
            <img
                src="{{ asset('images/' . $image) }}"
                class="w-24 h-24 md:w-32 md:h-32 object-cover rounded mb-3 mx-auto"
                alt="{{ $material->category }}"
            >

            <h3 class="text-xl font-bold min-h-[64px]">
                {{ $material->name }}
            </h3>

            <p class="text-sm text-gray-500">
                {{ $material->category }}
            </p>

            <p class="text-sm mt-2">
                Voorraad: {{ $stock }}
            </p>
        </a>

        <div class="mt-4">
            @if($isOutOfStock)
                <button
                    type="button"
                    disabled
                    class="w-full bg-gray-300 text-gray-500 py-2 rounded-lg cursor-not-allowed"
                >
                    Niet beschikbaar
                </button>
            @else
                <form action="{{ route('cart.add', $material->id) }}" method="POST" class="js-add-to-cart">
                    @csrf

                    <button
                        type="submit"
                        class="w-full bg-[#0F4C81] hover:bg-[#1E6BA8] text-white py-2 rounded-lg transition"
                    >
                        + Toevoegen
                    </button>
                </form>
            @endif
        </div>
    </div>
@else
    <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition overflow-hidden">
        <a href="{{ route('technician.materials.show', $material->id) }}" class="block">
            <img
                src="{{ asset('images/' . $image) }}"
                class="w-full h-48 object-cover"
                alt="{{ $material->category }}"
            >
        </a>

        <div class="p-5 flex flex-col min-h-[260px]">
            <a href="{{ route('technician.materials.show', $material->id) }}" class="block">
                <p class="text-xs text-gray-400 uppercase mb-1">
                    {{ $material->category }}
                </p>

                <h3 class="text-xl font-bold text-gray-800 mb-3 min-h-[64px]">
                    {{ $material->name }}
                </h3>
            </a>

            <div class="flex justify-between items-center mb-4">
                <span class="text-sm text-gray-500">
                    Voorraad
                </span>

                <span class="font-bold {{ $isOutOfStock || $isLowStock ? 'text-red-600' : 'text-green-600' }}">
                    {{ $stock }}
                </span>
            </div>

            @if($isOutOfStock)
                <span class="inline-block bg-red-100 text-red-700 text-xs px-3 py-1 rounded-full">
                    Geen voorraad
                </span>
            @elseif($isLowStock)
                <span class="inline-block bg-red-100 text-red-700 text-xs px-3 py-1 rounded-full">
                    Lage voorraad
                </span>
            @else
                <span class="inline-block bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full">
                    Beschikbaar
                </span>
            @endif

            <div class="mt-auto">
                @if($isOutOfStock)
                    <button
                        type="button"
                        disabled
                        class="w-full mt-5 bg-gray-300 text-gray-500 font-semibold py-3 rounded-xl cursor-not-allowed"
                    >
                        Niet beschikbaar
                    </button>
                @else
                    <form action="{{ route('cart.add', $material->id) }}" method="POST" class="mt-5 js-add-to-cart">
                        @csrf

                        <button
                            type="submit"
                            class="w-full bg-[#0F4C81] hover:bg-[#1E6BA8] text-white font-semibold py-3 rounded-xl transition"
                        >
                            + Toevoegen
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endif
