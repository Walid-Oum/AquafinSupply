@php
    $localStock = $material->stocks->first();
    $stock = $localStock?->stock ?? 0;
    $minimumStock = $localStock?->minimum_stock ?? 0;

    $categoryImages = [
        'Aquafin tools' => 'aquafintools.png',
        'Bevestigingsmateriaal' => 'bevestigingsmateriaal.png',
        'Gereedschap' => 'gereedschap.png',
        'PBM' => 'PBM.png',
        'Technisch onderhoud' => 'technischeonderhoud.png',
        'Verbruiksgoederen' => 'verbruiksgoederen.png',
    ];
@endphp
@if($compact)
    <a
        href="{{ route('technician.materials.show', $material->id) }}"
        class="border rounded-lg p-4 bg-white hover:shadow block">

        <img
    src="{{ asset('images/' . ($categoryImages[$material->category] ?? 'sidebar-bg.jpg')) }}"
    class="w-24 h-24 md:w-32 md:h-32 object-cover rounded mb-3 mx-auto"
    alt="{{ $material->category }}">

      <h3 class="text-xl font-bold min-h-[64px]">
    {{ $material->name }}
</h3>

        <p class="text-sm text-gray-500">
            {{ $material->category }}
        </p>

        <p class="text-sm mt-2">
            Voorraad: {{ $stock }}
        </p>

        <form
            action="{{ route('cart.add', $material->id) }}"
            method="POST"
            class="mt-4"
            onclick="event.stopPropagation();">

            @csrf

            <button
                type="submit"
                onclick="event.stopPropagation();"
                class="w-full bg-[#0F4C81] hover:bg-[#1E6BA8] text-white py-2 rounded-lg">
                + Toevoegen
            </button>
        </form>
    </a>
@else
  <a
        href="{{ route('technician.materials.show', $material->id) }}"
        class="bg-white rounded-2xl shadow-md hover:shadow-xl transition overflow-hidden block">

        <img
            src="{{ asset('images/' . ($categoryImages[$material->category] ?? 'sidebar-bg.jpg')) }}"
            class="w-full h-48 object-cover"
            alt="{{ $material->category }}">

        <div class="p-5 flex flex-col min-h-[260px]">


            <p class="text-xs text-gray-400 uppercase mb-1">
                {{ $material->category }}
            </p>

            <h3 class="text-xl font-bold text-gray-800 mb-3 min-h-[64px]">
    {{ $material->name }}
</h3>

            <div class="flex justify-between items-center mb-4">
                <span class="text-sm text-gray-500">
                    Voorraad
                </span>

                <span class="font-bold {{ $stock <= $minimumStock ? 'text-red-600' : 'text-green-600' }}">
                    {{ $stock }}
                </span>
            </div>

            @if($stock <= $minimumStock)
                <span class="inline-block bg-red-100 text-red-700 text-xs px-3 py-1 rounded-full">
                    Lage voorraad
                </span>
            @else
                <span class="inline-block bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full">
                    Beschikbaar
                </span>
            @endif
<div class="mt-auto">
            <form
                action="{{ route('cart.add', $material->id) }}"
                method="POST"
                class="mt-5"
                onclick="event.stopPropagation();">

                @csrf

                <button
                    type="submit"
                    onclick="event.stopPropagation();"
                    class="w-full bg-[#0F4C81] hover:bg-[#1E6BA8] text-white font-semibold py-3 rounded-xl">
                    + Toevoegen
                </button>
            </form>
            </div>
        </div>
    </a>
@endif
