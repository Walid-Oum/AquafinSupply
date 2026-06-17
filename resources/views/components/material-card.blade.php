@props([
    'material',
    'compact' => false,
])

@php
    $localStock = $material->stocks->first();
    $stock = $localStock?->stock ?? 0;
    $minimumStock = $localStock?->minimum_stock ?? 0;

    $isOutOfStock = $stock <= 0;
    $isLowStock = ! $isOutOfStock && $stock <= $minimumStock;

    $categoryImages = [
        'Aquafin tools' => 'aquafintools.png',
        'Bevestigingsmateriaal' => 'bevestigingsmateriaal.png',
        'Gereedschap' => 'gereedschap.png',
        'PBM' => 'PBM.png',
        'Technisch onderhoud' => 'technischeonderhoud.png',
        'Verbruiksgoederen' => 'verbruiksgoederen.png',
    ];

    $fallbackImage = $categoryImages[$material->category] ?? 'sidebar-bg.jpg';

    $imageUrl = $material->image
        ? \Illuminate\Support\Facades\Storage::url($material->image)
        : asset('images/' . $fallbackImage);

    if ($isOutOfStock) {
        $stockBadgeText = 'Geen voorraad';
        $stockBadgeClasses = 'bg-red-100 text-red-700';
        $stockTextClasses = 'text-red-600';
    } elseif ($isLowStock) {
        $stockBadgeText = 'Lage voorraad';
        $stockBadgeClasses = 'bg-orange-100 text-orange-700';
        $stockTextClasses = 'text-orange-600';
    } else {
        $stockBadgeText = 'Beschikbaar';
        $stockBadgeClasses = 'bg-green-100 text-green-700';
        $stockTextClasses = 'text-green-600';
    }
@endphp

@if($compact)
    <div class="h-full rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md">
        <a href="{{ route('technician.materials.show', $material->id) }}" class="block">
            <img
                src="{{ $imageUrl }}"
                class="mx-auto mb-2 h-20 w-20 rounded-lg object-cover"
                alt="{{ $material->name }}"
            >

            <p class="mb-1 text-xs uppercase text-gray-400">
                {{ $material->category }}
            </p>

            <h3 class="min-h-[44px] text-base font-bold text-gray-800">
                {{ $material->name }}
            </h3>

            <div class="mt-3 flex items-center justify-between">
                <span class="text-sm text-gray-500">
                    Voorraad
                </span>

                <span class="font-bold {{ $stockTextClasses }}">
                    {{ $stock }}
                </span>
            </div>

            <span class="mt-3 inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $stockBadgeClasses }}">
                {{ $stockBadgeText }}
            </span>
        </a>

        <div class="mt-3">
            @if($isOutOfStock)
                <button
                    type="button"
                    disabled
                    class="w-full rounded-lg bg-gray-300 py-2 font-semibold text-gray-500 cursor-not-allowed"
                >
                    Niet beschikbaar
                </button>
            @else
                <form
                    action="{{ route('cart.add', $material->id) }}"
                    method="POST"
                    class="js-add-to-cart"
                >
                    @csrf

                    <x-button type="submit" class="w-full justify-center">
                        + Toevoegen
                    </x-button>
                </form>
            @endif
        </div>
    </div>
@else
    <div class="h-full overflow-hidden rounded-2xl bg-white shadow-md transition hover:shadow-xl">
        <a href="{{ route('technician.materials.show', $material->id) }}" class="block">
            <img
                src="{{ $imageUrl }}"
                class="h-48 w-full object-cover"
                alt="{{ $material->name }}"
            >
        </a>

        <div class="flex min-h-[260px] flex-col p-5">
            <a href="{{ route('technician.materials.show', $material->id) }}" class="block">
                <p class="mb-1 text-xs uppercase text-gray-400">
                    {{ $material->category }}
                </p>

                <h3 class="mb-3 min-h-[64px] text-xl font-bold text-gray-800">
                    {{ $material->name }}
                </h3>
            </a>

            <div class="mb-4 flex items-center justify-between">
                <span class="text-sm text-gray-500">
                    Voorraad
                </span>

                <span class="font-bold {{ $stockTextClasses }}">
                    {{ $stock }}
                </span>
            </div>

            <span class="inline-block w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $stockBadgeClasses }}">
                {{ $stockBadgeText }}
            </span>

            <div class="mt-auto">
                @if($isOutOfStock)
                    <button
                        type="button"
                        disabled
                        class="mt-5 w-full rounded-xl bg-gray-300 py-3 font-semibold text-gray-500 cursor-not-allowed"
                    >
                        Niet beschikbaar
                    </button>
                @else
                    <form
                        action="{{ route('cart.add', $material->id) }}"
                        method="POST"
                        class="js-add-to-cart mt-5"
                    >
                        @csrf

                        <x-button type="submit" class="w-full justify-center">
                            + Toevoegen
                        </x-button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endif
