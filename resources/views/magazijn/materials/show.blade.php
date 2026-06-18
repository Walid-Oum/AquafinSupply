{{--
    MAGAZIJN - MATERIAAL DETAILS

    @author     
    @version     1.0
    @since       2026-06-18

    Deze view toont de detailgegevens van een specifiek materiaal voor
    magazijnmedewerkers. Hier worden alle eigenschappen getoond: naam,
    categorie, beschrijving, voorraadstatus, minimum voorraad en
    de risiconiveaus die aan het materiaal zijn gekoppeld.

    @see App\Http\Controllers\MaterialController::show()
--}}

@php
    // Haal de voorraadgegevens op voor het huidige depot van de magazijnmedewerker
    $localStock = $material->stocks->first();

    $stock = $localStock?->stock ?? 0;
    $minimumStock = $localStock?->minimum_stock ?? $material->minimum_stock ?? 0;

    // Categorie-afbeeldingen voor fallback (als er geen eigen afbeelding is)
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
        ? asset('storage/' . $material->image)
        : asset('images/' . $fallbackImage);

    // Bepaal de voorraadstatus op basis van de voorraad ten opzichte van de minimum voorraad
    if ($stock <= 0) {
        $stockStatusText = 'Geen voorraad';
        $stockStatusClasses = 'bg-red-100 text-red-700';
        $stockColorClass = 'text-red-600';
    } elseif ($stock <= $minimumStock) {
        $stockStatusText = 'Lage voorraad';
        $stockStatusClasses = 'bg-orange-100 text-orange-700';
        $stockColorClass = 'text-orange-600';
    } else {
        $stockStatusText = 'OK';
        $stockStatusClasses = 'bg-green-100 text-green-700';
        $stockColorClass = 'text-green-600';
    }
@endphp

<x-app-layout>
    <div class="space-y-6">
        {{-- HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <x-page-header title="Materiaal details" />

                <p class="mt-1 text-sm text-gray-600 sm:text-base">
                    Bekijk de gegevens, voorraadstatus en risiconiveaus van dit materiaal.
                </p>
            </div>

            <a
                href="{{ route('magazijn.materials.index') }}"
                class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
            >
                ← Terug naar voorraad
            </a>
        </div>

        {{-- MATERIAAL DETAILS CARD --}}
        <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-5 flex flex-col gap-3 border-b border-gray-100 pb-5 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-gray-400">
                        {{ $material->category }}
                    </p>

                    <h2 class="mt-1 text-2xl font-bold text-[#0F4C81]">
                        {{ $material->name }}
                    </h2>
                </div>

                <span class="inline-flex w-fit rounded-full px-3 py-1 text-sm font-semibold {{ $stockStatusClasses }}">
                    {{ $stockStatusText }}
                </span>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(260px,380px)_1fr] lg:items-start">
                {{-- AFBEELDING --}}
                <div class="rounded-2xl bg-gray-50 p-4">
                    <div class="flex min-h-56 items-center justify-center rounded-xl bg-white p-4 sm:min-h-72">
                        <img
                            src="{{ $imageUrl }}"
                            class="max-h-72 max-w-full object-contain"
                            alt="{{ $material->name }}"
                        >
                    </div>
                </div>

                {{-- INFORMATIE --}}
                <div class="space-y-5">
                    <div>
                        <p class="text-sm font-semibold text-gray-500">
                            Beschrijving
                        </p>

                        <p class="mt-1 leading-relaxed text-gray-900">
                            {{ $material->description ?? 'Geen beschrijving beschikbaar.' }}
                        </p>
                    </div>

                    {{-- RISICONIVEAUS --}}
                    <div>
                        <p class="mb-2 text-sm font-semibold text-gray-500">
                            Risiconiveau
                        </p>

                        <div class="flex flex-wrap gap-2">
                            @forelse($material->riskLevels as $riskLevel)
                                <span
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                    @if($riskLevel->name === 'Hoog')
                                        bg-red-100 text-red-700
                                    @elseif($riskLevel->name === 'Gemiddeld')
                                        bg-yellow-100 text-yellow-700
                                    @else
                                        bg-green-100 text-green-700
                                    @endif"
                                >
                                    {{ $riskLevel->name }}
                                </span>
                            @empty
                                <span class="text-sm text-gray-500">
                                    Geen risiconiveau gekoppeld
                                </span>
                            @endforelse
                        </div>
                    </div>

                    {{-- VOORRAADGEGEVENS --}}
                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                        <p class="mb-4 text-sm font-semibold text-gray-700">
                            Voorraadgegevens
                        </p>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Voorraad
                                </p>

                                <p class="mt-1 text-2xl font-bold {{ $stockColorClass }}">
                                    {{ $stock }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Minimum
                                </p>

                                <p class="mt-1 text-2xl font-bold text-gray-900">
                                    {{ $minimumStock }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 border-t border-gray-200 pt-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Status
                            </p>

                            <span class="mt-2 inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $stockStatusClasses }}">
                                {{ $stockStatusText }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- TERUG KNOP --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
            <a
                href="{{ route('magazijn.materials.index') }}"
                class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
            >
                Terug
            </a>
        </div>
    </div>
</x-app-layout>