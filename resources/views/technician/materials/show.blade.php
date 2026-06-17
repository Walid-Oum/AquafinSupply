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

<x-app-layout>
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-page-header title="Materiaal detail" />

        <a
            href="{{ route('technician.materials.index') }}"
            class="inline-flex w-fit items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
        >
            ← Terug naar materialen
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Afbeelding --}}
        <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="flex min-h-[260px] items-center justify-center bg-gray-50 p-6 sm:min-h-[360px]">
                <img
                    src="{{ $imageUrl }}"
                    class="max-h-[300px] max-w-full object-contain sm:max-h-[420px]"
                    alt="{{ $material->name }}"
                >
            </div>
        </section>

        {{-- Info --}}
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">
                    {{ $material->category }}
                </p>

                <h1 class="text-2xl font-bold leading-tight text-[#0F4C81] sm:text-3xl">
                    {{ $material->name }}
                </h1>
            </div>

            <div class="mb-6 flex flex-wrap gap-2">
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $stockBadgeClasses }}">
                    {{ $stockBadgeText }}
                </span>

                @if($material->is_active)
                    <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                        Actief
                    </span>
                @else
                    <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                        Inactief
                    </span>
                @endif
            </div>

            <div class="space-y-4">
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <p class="mb-1 text-sm font-semibold text-gray-500">
                        Beschrijving
                    </p>

                    <p class="text-gray-800">
                        {{ $material->description ?? 'Geen beschrijving' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                        <p class="mb-1 text-sm font-semibold text-gray-500">
                            Voorraad
                        </p>

                        <p class="text-2xl font-bold {{ $stockTextClasses }}">
                            {{ $stock }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                        <p class="mb-1 text-sm font-semibold text-gray-500">
                            Minimumvoorraad
                        </p>

                        <p class="text-2xl font-bold text-gray-800">
                            {{ $minimumStock }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
                @if($stock > 0 && $material->is_active)
                    <form
                        action="{{ route('cart.add', $material->id) }}"
                        method="POST"
                        class="w-full sm:w-auto"
                    >
                        @csrf

                        <x-button type="submit" class="w-full justify-center sm:w-auto">
                            🛒 Toevoegen aan winkelmandje
                        </x-button>
                    </form>
                @else
                    <button
                        type="button"
                        disabled
                        class="w-full cursor-not-allowed rounded-xl bg-gray-300 px-5 py-3 font-semibold text-gray-500 sm:w-auto"
                    >
                        Niet beschikbaar
                    </button>
                @endif

                <a
                    href="{{ route('technician.materials.index') }}"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-gray-500 px-5 py-3 font-semibold text-white transition hover:bg-gray-600 sm:w-auto"
                >
                    Terug
                </a>
            </div>
        </section>
    </div>
</x-app-layout>
