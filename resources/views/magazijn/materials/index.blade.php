{{--
    MAGAZIJN - VOORRAAD OVERZICHT

    @author      Chayma (Team Aquafin)
    @version     1.0
    @since       2026-06-18

    Deze view toont het voorraad overzicht voor magazijnmedewerkers.
    Magazijnmedewerkers kunnen hier de voorraad van materialen in hun eigen depot
    bekijken en aanpassen. De view bevat zoek-, filter- en sorteer functionaliteit.
    Ook wordt de voorraadstatus (laag/OK) weergegeven met kleurindicatie.

    @see App\Http\Controllers\MaterialController::warehouseIndex()
    @see App\Http\Controllers\MaterialController::warehouseUpdate()
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
    <div class="space-y-6">
        <div>
            <x-page-header title="Voorraad overzicht"/>

            <p class="mt-1 text-sm text-gray-600 sm:text-base">
                Bekijk en beheer de voorraad van materialen in jouw depot.
            </p>
        </div>

        {{-- FILTERS EN ZOEKBALK --}}
        <section class="rounded-2xl border border-gray-100 bg-white p-3 shadow-sm sm:p-4">
            <div class="grid grid-cols-1 gap-3 xl:grid-cols-4 xl:items-center">
                <div class="xl:col-span-1">
                    <input
                        type="text"
                        id="global-material-search"
                        autocomplete="off"
                        placeholder="Materiaal zoeken..."
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                    >
                </div>

                <form
                    method="GET"
                    action="{{ route('magazijn.materials.index') }}"
                    class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:col-span-3 xl:flex xl:justify-end"
                >
                    <select
                        name="category"
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20 xl:w-56"
                    >
                        <option value="">
                            Alle categorieën
                        </option>

                        @foreach($categories as $category)
                            <option
                                value="{{ $category }}"
                                {{ request('category') == $category ? 'selected' : '' }}
                            >
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>

                    <select
                        name="stock_status"
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20 xl:w-56"
                    >
                        <option value="">
                            Alle voorraadstatussen
                        </option>

                        <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>
                            Lage voorraad
                        </option>

                        <option value="ok" {{ request('stock_status') === 'ok' ? 'selected' : '' }}>
                            OK
                        </option>
                    </select>

                    <x-button type="submit" class="w-full justify-center sm:w-auto">
                        Filter
                    </x-button>

                    <a
                        href="{{ route('magazijn.materials.index') }}"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
                    >
                        Reset
                    </a>
                </form>
            </div>
        </section>

        {{-- MATERIALEN LIJST (MOBILE + DESKTOP) --}}
        <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
            @if($materials->count() > 0)
                {{-- Mobile card layout --}}
                <div class="space-y-3 p-4 md:hidden">
                    @foreach($materials as $material)
                        @php
                            $localStock = $material->stocks->first();
                            $stock = $localStock?->stock ?? 0;
                            $minimumStock = $localStock?->minimum_stock ?? 0;

                            $fallbackImage = $categoryImages[$material->category] ?? 'sidebar-bg.jpg';

                            $imageUrl = $material->image
                                ? asset('storage/' . $material->image)
                                : asset('images/' . $fallbackImage);

                            $isLowStock = $stock <= $minimumStock;
                        @endphp

                        <article
                            class="js-material-item material-row rounded-2xl border border-gray-100 bg-gray-50 p-4 shadow-sm"
                            data-search="{{ $material->name }} {{ $material->category }}"
                        >
                            <div class="flex gap-4">
                                <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-xl bg-white p-2">
                                    <img
                                        src="{{ $imageUrl }}"
                                        alt="{{ $material->name }}"
                                        class="max-h-full max-w-full object-contain"
                                    >
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="material-category text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        {{ $material->category }}
                                    </p>

                                    <h2 class="material-name mt-1 font-bold leading-snug text-gray-900">
                                        {{ $material->name }}
                                    </h2>

                                    <div class="mt-3 flex flex-wrap items-center gap-2">
                                        @if($isLowStock)
                                            <span class="rounded-full bg-red-100 px-3 py-1 text-sm font-bold text-red-700">
                                                Voorraad: {{ $stock }}
                                            </span>
                                        @else
                                            <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-bold text-green-700">
                                                Voorraad: {{ $stock }}
                                            </span>
                                        @endif

                                        <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-gray-600">
                                            Minimum: {{ $minimumStock }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Voorraad update formulier (mobile) --}}
                            <div class="mt-4 rounded-xl bg-white p-3">
                                <label
                                    for="stock-mobile-{{ $material->id }}"
                                    class="mb-2 block text-sm font-semibold text-gray-700"
                                >
                                    Nieuwe voorraad
                                </label>

                                <form
                                    id="stock-form-mobile-{{ $material->id }}"
                                    action="{{ route('magazijn.materials.update', $material->id) }}"
                                    method="POST"
                                    class="flex items-center gap-3"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <input
                                        id="stock-mobile-{{ $material->id }}"
                                        type="number"
                                        name="stock"
                                        value="{{ $stock }}"
                                        min="0"
                                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-center text-sm font-semibold focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                                    >

                                    <x-button type="submit" class="shrink-0 justify-center">
                                        Opslaan
                                    </x-button>
                                </form>
                            </div>

                            <div class="mt-3">
                                <a
                                    href="{{ route('magazijn.materials.show', $material->id) }}"
                                    class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 text-sm font-semibold text-[#0F4C81] transition hover:bg-gray-200"
                                >
                                    Bekijk materiaal
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Desktop table layout --}}
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full min-w-[900px]">
                        <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-4 text-left font-semibold text-gray-700">
                                Afbeelding
                            </th>

                            <th class="p-4 text-left font-semibold text-gray-700">
                                Naam
                            </th>

                            <th class="p-4 text-left font-semibold text-gray-700">
                                Categorie
                            </th>

                            <th class="p-4 text-left font-semibold text-gray-700">
                                Voorraad in jouw depot
                            </th>

                            <th class="p-4 text-left font-semibold text-gray-700">
                                Nieuwe voorraad
                            </th>

                            <th class="p-4 text-center font-semibold text-gray-700">
                                Opslaan
                            </th>

                            <th class="p-4 text-center font-semibold text-gray-700">
                                Bekijk
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($materials as $material)
                            @php
                                $localStock = $material->stocks->first();
                                $stock = $localStock?->stock ?? 0;
                                $minimumStock = $localStock?->minimum_stock ?? 0;

                                $fallbackImage = $categoryImages[$material->category] ?? 'sidebar-bg.jpg';

                                $imageUrl = $material->image
                                    ? asset('storage/' . $material->image)
                                    : asset('images/' . $fallbackImage);

                                $isLowStock = $stock <= $minimumStock;
                            @endphp

                            <tr
                                class="js-material-item material-row border-b border-gray-100 transition-colors hover:bg-gray-50 last:border-0"
                                data-search="{{ $material->name }} {{ $material->category }}"
                            >
                                <td class="p-4">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-xl border border-gray-100 bg-gray-50 p-2">
                                        <img
                                            src="{{ $imageUrl }}"
                                            alt="{{ $material->name }}"
                                            class="max-h-full max-w-full object-contain"
                                        >
                                    </div>
                                </td>

                                <td class="material-name p-4 font-medium text-gray-800">
                                    {{ $material->name }}
                                </td>

                                <td class="material-category p-4 text-gray-600">
                                    {{ $material->category }}
                                </td>

                                <td class="p-4">
                                    @if($isLowStock)
                                        <span class="font-bold text-red-600">
                                            {{ $stock }}
                                        </span>
                                    @else
                                        <span class="font-semibold text-green-600">
                                            {{ $stock }}
                                        </span>
                                    @endif
                                </td>

                                <td class="p-4 text-center">
                                    {{-- Voorraad update formulier (desktop) --}}
                                    <form
                                        id="stock-form-desktop-{{ $material->id }}"
                                        action="{{ route('magazijn.materials.update', $material->id) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="number"
                                            name="stock"
                                            value="{{ $stock }}"
                                            min="0"
                                            class="w-24 rounded-lg border border-gray-200 px-3 py-2 text-center focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                                        >
                                    </form>
                                </td>

                                <td class="p-4 text-center">
                                    <x-button form="stock-form-desktop-{{ $material->id }}">
                                        Opslaan
                                    </x-button>
                                </td>

                                <td class="p-4 text-center">
                                    <a
                                        href="{{ route('magazijn.materials.show', $material->id) }}"
                                        class="font-semibold text-[#0F4C81] hover:underline"
                                    >
                                        Bekijk
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div
                    id="no-materials-backend"
                    class="px-4 py-10 text-center text-gray-500 italic"
                >
                    Geen materialen gevonden.
                </div>
            @endif

            <div
                id="no-materials-found-row"
                class="hidden px-4 py-10 text-center text-gray-500 italic"
            >
                Geen materialen gevonden die voldoen aan de zoekterm.
            </div>
        </section>
    </div>

    {{-- ZOEKFUNCTIE MET LEVENSHTEIN ALGORITME --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            /**
             * Zoek functionaliteit voor materialen.
             * Gebruikt Levenshtein afstand voor spellingsfouten tolerantie.
             */
            const searchInput = document.getElementById('global-material-search');
            const materialItems = document.querySelectorAll('.js-material-item');
            const noResultsRow = document.getElementById('no-materials-found-row');
            const backendEmptyRow = document.getElementById('no-materials-backend');

            function normalizeText(text) {
                if (!text) return '';

                return text.toLowerCase()
                    .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
                    .replace(/[^a-z0-9]/g, ' ')
                    .replace(/\s+/g, ' ')
                    .trim();
            }

            /**
             * Levenshtein afstand berekenen tussen twee woorden.
             * Wordt gebruikt om spellingsfouten te tolereren bij zoeken.
             */
            function levenshtein(a, b) {
                const matrix = [];

                for (let i = 0; i <= b.length; i++) {
                    matrix[i] = [i];
                }

                for (let j = 0; j <= a.length; j++) {
                    matrix[0][j] = j;
                }

                for (let i = 1; i <= b.length; i++) {
                    for (let j = 1; j <= a.length; j++) {
                        if (b.charAt(i - 1) === a.charAt(j - 1)) {
                            matrix[i][j] = matrix[i - 1][j - 1];
                        } else {
                            matrix[i][j] = Math.min(
                                matrix[i - 1][j - 1] + 1,
                                matrix[i][j - 1] + 1,
                                matrix[i - 1][j] + 1
                            );
                        }
                    }
                }

                return matrix[b.length][a.length];
            }

            function getAllowedDistance(word) {
                const length = word.length;

                if (length <= 4) {
                    return 1;
                }

                if (length <= 7) {
                    return 2;
                }

                if (length <= 12) {
                    return 3;
                }

                return 4;
            }

            /**
             * Controleert of een item overeenkomt met de zoekterm.
             * Gebruikt exacte overeenkomst, substrings en Levenshtein afstand.
             */
            function itemMatchesQuery(item, queryClean) {
                const nameEl = item.querySelector('.material-name');
                const categoryEl = item.querySelector('.material-category');

                const name = normalizeText(nameEl ? nameEl.textContent : '');
                const category = normalizeText(categoryEl ? categoryEl.textContent : '');

                const flatQuery = queryClean.replace(/ /g, '');
                const flatName = name.replace(/ /g, '');
                const flatCategory = category.replace(/ /g, '');

                if (
                    name.includes(queryClean) ||
                    category.includes(queryClean) ||
                    flatName.includes(flatQuery) ||
                    flatCategory.includes(flatQuery)
                ) {
                    return true;
                }

                if (flatQuery.length < 3) {
                    return false;
                }

                const allowedDistance = getAllowedDistance(flatQuery);

                if (
                    levenshtein(flatQuery, flatName) <= allowedDistance ||
                    levenshtein(flatQuery, flatCategory) <= allowedDistance
                ) {
                    return true;
                }

                const queryWords = queryClean.split(' ');
                const nameWords = name.split(' ');

                for (const qWord of queryWords) {
                    for (const nWord of nameWords) {
                        if (nWord.includes(qWord) || qWord.includes(nWord)) {
                            return true;
                        }

                        if (qWord.length >= 3 && levenshtein(qWord, nWord) <= getAllowedDistance(qWord)) {
                            return true;
                        }
                    }
                }

                return false;
            }

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const queryClean = normalizeText(this.value);
                    let visibleCount = 0;

                    if (queryClean === '') {
                        materialItems.forEach(function (item) {
                            item.classList.remove('hidden');
                        });

                        if (noResultsRow) {
                            noResultsRow.classList.add('hidden');
                        }

                        if (backendEmptyRow) {
                            backendEmptyRow.classList.remove('hidden');
                        }

                        return;
                    }

                    if (backendEmptyRow) {
                        backendEmptyRow.classList.add('hidden');
                    }

                    materialItems.forEach(function (item) {
                        const isMatch = itemMatchesQuery(item, queryClean);

                        if (isMatch) {
                            item.classList.remove('hidden');
                            visibleCount++;
                        } else {
                            item.classList.add('hidden');
                        }
                    });

                    if (noResultsRow) {
                        if (visibleCount === 0) {
                            noResultsRow.classList.remove('hidden');
                        } else {
                            noResultsRow.classList.add('hidden');
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>