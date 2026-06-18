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
    <div class="flex h-full flex-col rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:shadow-md">
        <a href="{{ route('technician.materials.show', $material->id) }}" class="block">
            <div class="mb-3 flex h-24 items-center justify-center rounded-xl bg-gray-50 p-3">
                <img
                    src="{{ $imageUrl }}"
                    class="max-h-full max-w-full object-contain"
                    alt="{{ $material->name }}"
                >
            </div>

            <p class="mb-1 truncate text-xs uppercase tracking-wide text-gray-400">
                {{ $material->category }}
            </p>

            <h3 class="line-clamp-2 min-h-[48px] text-base font-bold leading-snug text-gray-800">
                {{ $material->name }}
            </h3>
        </a>

        <div class="mt-3 flex items-center justify-between">
            <span class="text-sm text-gray-500">
                Voorraad
            </span>

            <span class="font-bold {{ $stockTextClasses }}">
                {{ $stock }}
            </span>
        </div>

        <span class="mt-3 inline-block w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $stockBadgeClasses }}">
            {{ $stockBadgeText }}
        </span>

        <div class="mt-auto pt-3">
            @if($isOutOfStock)
                <button
                    type="button"
                    disabled
                    class="w-full cursor-not-allowed rounded-xl bg-gray-300 py-2.5 text-sm font-semibold text-gray-500"
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

                    <x-button type="submit" class="w-full justify-center text-sm">
                        + Toevoegen
                    </x-button>
                </form>
            @endif
        </div>
    </div>
@else
    <div class="flex h-full flex-col overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-gray-100 transition hover:shadow-xl">
        <a href="{{ route('technician.materials.show', $material->id) }}" class="block">
            <div class="flex h-40 items-center justify-center bg-gray-50 p-4 sm:h-44 lg:h-48">
                <img
                    src="{{ $imageUrl }}"
                    class="max-h-full max-w-full object-contain"
                    alt="{{ $material->name }}"
                >
            </div>
        </a>

        <div class="flex flex-1 flex-col p-4 sm:p-5">
            <a href="{{ route('technician.materials.show', $material->id) }}" class="block">
                <p class="mb-1 truncate text-xs uppercase tracking-wide text-gray-400">
                    {{ $material->category }}
                </p>

                <h3 class="mb-3 line-clamp-2 min-h-[56px] text-lg font-bold leading-snug text-gray-800 sm:text-xl">
                    {{ $material->name }}
                </h3>
            </a>

            <div class="mb-3 flex items-center justify-between">
                <span class="text-sm text-gray-500">
                    Voorraad
                </span>

                <span class="text-lg font-bold {{ $stockTextClasses }}">
                    {{ $stock }}
                </span>
            </div>

            <span class="inline-block w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $stockBadgeClasses }}">
                {{ $stockBadgeText }}
            </span>

            <div class="mt-auto pt-4">
                @if($isOutOfStock)
                    <button
                        type="button"
                        disabled
                        class="w-full cursor-not-allowed rounded-xl bg-gray-300 py-3 font-semibold text-gray-500"
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
    </div>
@endif
