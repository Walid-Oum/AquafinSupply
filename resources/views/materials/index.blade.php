<x-app-layout>
    <div class="min-w-0 max-w-full space-y-6 overflow-x-hidden">
        {{-- HEADER --}}
        <div>
            <x-page-header title="Materialen overzicht" />
        </div>

        {{-- ACTIES + ZOEKEN EN FILTEREN --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <a href="{{ route('materials.create') }}" class="w-full sm:w-auto">
                <x-button class="w-full justify-center sm:w-auto">
                    + Nieuw materiaal
                </x-button>
            </a>

            <div class="flex w-full flex-col gap-3 lg:w-auto lg:flex-row lg:items-center">
                <div class="w-full lg:w-72">
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
                    action="{{ route('materials.index') }}"
                    class="flex w-full flex-col gap-3 sm:flex-row lg:w-auto"
                >
                    <select
                        name="category"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20 sm:w-auto"
                    >
                        <option value="">Alle categorieën</option>

                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>

                    <select
                        name="stock_status"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20 sm:w-auto"
                    >
                        <option value="">Alle voorraadstatussen</option>

                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>
                            Lage voorraad
                        </option>

                        <option value="ok" {{ request('stock_status') == 'ok' ? 'selected' : '' }}>
                            OK
                        </option>
                    </select>

                    <x-button class="w-full justify-center sm:w-auto">
                        Filter
                    </x-button>

                    <a
                        href="{{ route('materials.index') }}"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
                    >
                        Reset
                    </a>
                </form>
            </div>
        </div>

        {{-- DESKTOPWEERGAVE --}}
        <div class="hidden rounded-2xl border border-gray-200 bg-white shadow-sm lg:block">
            <div class="w-full overflow-hidden rounded-2xl">
                <table class="w-full table-fixed divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="w-[22%] px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Naam
                        </th>

                        <th class="w-[18%] px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Categorie
                        </th>

                        <th class="w-[13%] px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Totale voorraad
                        </th>

                        <th class="w-[13%] px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Minimum per depot
                        </th>

                        <th class="w-[15%] px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Voorraadstatus
                        </th>

                        <th class="w-[10%] px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Status
                        </th>

                        <th class="w-[9%] px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Acties
                        </th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white" id="material-table-body">
                    @forelse($materials as $material)
                        @php
                            $totalStock = $material->stocks->sum('stock');
                            $minimumStock = $material->stocks->max('minimum_stock') ?? $material->minimum_stock;

                            $hasLowStock = $material->stocks->contains(function ($stock) {
                                return $stock->stock <= $stock->minimum_stock;
                            });

                            $lowStockDepots = $material->stocks->filter(function ($stock) {
                                return $stock->stock <= $stock->minimum_stock;
                            })->count();
                        @endphp

                        <tr
                            onclick="window.location='{{ route('materials.show', $material->id) }}'"
                            class="material-row cursor-pointer transition hover:bg-gray-50/70"
                        >
                            <td class="material-name px-4 py-4 font-medium text-gray-800">
                                <div class="truncate">
                                    {{ $material->name }}
                                </div>
                            </td>

                            <td class="material-category px-4 py-4 text-gray-600">
                                <div class="truncate">
                                    {{ $material->category }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                @if($hasLowStock)
                                    <span class="font-bold text-red-600">
                                            {{ $totalStock }}
                                        </span>
                                @else
                                    <span class="text-gray-800">
                                            {{ $totalStock }}
                                        </span>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-gray-700">
                                {{ $minimumStock }}
                            </td>

                            <td class="px-4 py-4">
                                @if($hasLowStock)
                                    <div class="space-y-1">
                                            <span class="inline-block rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                                Lage voorraad
                                            </span>

                                        <p class="text-xs font-medium text-red-600">
                                            {{ $lowStockDepots }} depot(s)
                                        </p>
                                    </div>
                                @else
                                    <span class="inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            OK
                                        </span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                @if($material->is_active)
                                    <span class="inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            Actief
                                        </span>
                                @else
                                    <span class="inline-block rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                            Inactief
                                        </span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <a
                                    href="{{ route('materials.show', $material->id) }}"
                                    onclick="event.stopPropagation();"
                                >
                                    <x-button>
                                        Bekijk
                                    </x-button>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr id="no-materials-backend">
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                Geen materialen gevonden.
                            </td>
                        </tr>
                    @endforelse

                    <tr id="no-materials-found-row" class="hidden">
                        <td colspan="7" class="bg-gray-50 px-4 py-8 text-center italic text-gray-500">
                            Geen materialen gevonden die voldoen aan de zoekterm.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MOBIELE WEERGAVE --}}
        <div class="space-y-4 lg:hidden">
            @forelse($materials as $material)
                @php
                    $totalStock = $material->stocks->sum('stock');

                    $hasLowStock = $material->stocks->contains(function ($stock) {
                        return $stock->stock <= $stock->minimum_stock;
                    });
                @endphp

                <article class="material-card rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                    <div class="space-y-4">
                        <div>
                            <h2 class="material-name-mobile text-lg font-bold text-gray-900">
                                {{ $material->name }}
                            </h2>

                            <p class="material-category-mobile mt-1 text-sm text-gray-500">
                                {{ $material->category }}
                            </p>
                        </div>

                        <div class="grid gap-2 text-sm text-gray-700">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-gray-500">Totale voorraad</span>

                                @if($hasLowStock)
                                    <span class="font-bold text-red-600">
                                        {{ $totalStock }}
                                    </span>
                                @else
                                    <span class="font-semibold text-gray-900">
                                        {{ $totalStock }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <span class="text-gray-500">Voorraadstatus</span>

                                @if($hasLowStock)
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        Lage voorraad
                                    </span>
                                @else
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        OK
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <span class="text-gray-500">Status</span>

                                @if($material->is_active)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Actief
                                    </span>
                                @else
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        Inactief
                                    </span>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('materials.show', $material->id) }}">
                            <x-button class="w-full justify-center">
                                Bekijk
                            </x-button>
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-gray-100 bg-white p-6 text-center text-gray-500 shadow-sm">
                    Geen materialen gevonden.
                </div>
            @endforelse

            <div
                id="no-materials-found-mobile"
                class="hidden rounded-2xl border border-gray-100 bg-white p-6 text-center text-gray-500 shadow-sm"
            >
                Geen materialen gevonden die voldoen aan de zoekterm.
            </div>
        </div>
    </div>

    {{-- CLIENT-SIDE ZOEKFUNCTIE --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('global-material-search');
            const tableRows = document.querySelectorAll('.material-row');
            const mobileCards = document.querySelectorAll('.material-card');
            const noResultsRow = document.getElementById('no-materials-found-row');
            const noResultsMobile = document.getElementById('no-materials-found-mobile');
            const backendEmptyRow = document.getElementById('no-materials-backend');

            function normalizeText(text) {
                if (!text) return '';

                return text.toLowerCase()
                    .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
                    .replace(/[^a-z0-9]/g, ' ')
                    .replace(/\s+/g, ' ')
                    .trim();
            }

            function levenshtein(a, b) {
                const matrix = [];

                for (let i = 0; i <= b.length; i++) matrix[i] = [i];
                for (let j = 0; j <= a.length; j++) matrix[0][j] = j;

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

                if (length <= 4) return 1;
                if (length <= 7) return 2;
                if (length <= 12) return 3;

                return 4;
            }

            function isMaterialMatch(name, category, query) {
                const flatQuery = query.replace(/ /g, '');
                const flatName = name.replace(/ /g, '');
                const flatCategory = category.replace(/ /g, '');

                if (
                    name.includes(query) ||
                    category.includes(query) ||
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

                const queryWords = query.split(' ');
                const nameWords = name.split(' ');

                for (const qWord of queryWords) {
                    for (const nWord of nameWords) {
                        if (
                            nWord.includes(qWord) ||
                            qWord.includes(nWord) ||
                            (qWord.length >= 3 && levenshtein(qWord, nWord) <= getAllowedDistance(qWord))
                        ) {
                            return true;
                        }
                    }
                }

                return false;
            }

            if (!searchInput) return;

            searchInput.addEventListener('input', function () {
                const query = normalizeText(this.value);
                let visibleRows = 0;
                let visibleCards = 0;

                if (query === '') {
                    tableRows.forEach(row => row.style.display = '');
                    mobileCards.forEach(card => card.classList.remove('hidden'));
                    noResultsRow?.classList.add('hidden');
                    noResultsMobile?.classList.add('hidden');

                    if (backendEmptyRow) {
                        backendEmptyRow.style.display = '';
                    }

                    return;
                }

                if (backendEmptyRow) {
                    backendEmptyRow.style.display = 'none';
                }

                tableRows.forEach(row => {
                    const name = normalizeText(row.querySelector('.material-name')?.textContent || '');
                    const category = normalizeText(row.querySelector('.material-category')?.textContent || '');

                    if (isMaterialMatch(name, category, query)) {
                        row.style.display = '';
                        visibleRows++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                mobileCards.forEach(card => {
                    const name = normalizeText(card.querySelector('.material-name-mobile')?.textContent || '');
                    const category = normalizeText(card.querySelector('.material-category-mobile')?.textContent || '');

                    if (isMaterialMatch(name, category, query)) {
                        card.classList.remove('hidden');
                        visibleCards++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                if (visibleRows === 0) {
                    noResultsRow?.classList.remove('hidden');
                } else {
                    noResultsRow?.classList.add('hidden');
                }

                if (visibleCards === 0) {
                    noResultsMobile?.classList.remove('hidden');
                } else {
                    noResultsMobile?.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
