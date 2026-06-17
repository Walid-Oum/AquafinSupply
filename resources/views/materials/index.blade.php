<x-app-layout>
    <x-page-header title="Materialen overzicht"/>

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('materials.create') }}">
            <x-button>
                + Nieuw materiaal
            </x-button>
        </a>

        <div class="flex gap-4 items-center">
            <div class="relative">
                <input
                    type="text"
                    id="global-material-search"
                    autocomplete="off"
                    placeholder="Materiaal zoeken..."
                    class="border rounded px-3 py-2 w-64 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F4C81]">
            </div>

            <form method="GET" action="{{ route('materials.index') }}" class="flex gap-2">
                <select name="category" class="border rounded px-3 py-2 text-sm">
                    <option value="">Alle categorieën</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
                
                <select name="stock_status" class="border rounded px-3 py-2 text-sm">
                    <option value="">Alle voorraadstatussen</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Lage voorraad</option>
                    <option value="ok" {{ request('stock_status') == 'ok' ? 'selected' : '' }}>OK</option>
                </select>

                <x-button>Filter</x-button>
                <a href="{{ route('materials.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-400 transition">Reset</a>
            </form>
        </div>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-[1100px] w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Naam</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Categorie</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Totale voorraad</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Minimum per depot</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Voorraadstatus</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Acties</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100" id="material-table-body">
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
                            class="material-row cursor-pointer hover:bg-gray-100 transition">
                            <td class="px-4 py-3 font-medium text-gray-800 material-name">
                                {{ $material->name }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 material-category">
                                {{ $material->category }}
                            </td>
                            <td class="px-4 py-3">
                                @if($hasLowStock)
                                    <span class="text-red-600 font-bold">{{ $totalStock }}</span>
                                @else
                                    <span class="text-gray-800">{{ $totalStock }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $minimumStock }}</td>
                            <td class="px-4 py-3">
                                @if($hasLowStock)
                                    <div class="space-y-1">
                                        <span class="inline-block bg-red-100 text-red-700 text-xs font-semibold px-3 py-1 rounded-full">Lage voorraad</span>
                                        <p class="text-xs text-red-600 font-medium">{{ $lowStockDepots }} depot(s) onder minimum</p>
                                    </div>
                                @else
                                    <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">OK</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($material->is_active)
                                    <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">Actief</span>
                                @else
                                    <span class="inline-block bg-red-100 text-red-700 text-xs font-semibold px-3 py-1 rounded-full">Inactief</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('materials.show', $material->id) }}" onclick="event.stopPropagation();">
                                    <x-button>Bekijk</x-button>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr id="no-materials-backend">
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                Geen materialen gevonden.
                            </td>
                        </tr>
                    @endforelse

                    <tr id="no-materials-found-row" class="hidden">
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500 italic bg-gray-50">
                            Geen materialen gevonden die voldoen aan de zoekterm.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('global-material-search');
            const tableRows = document.querySelectorAll('.material-row');
            const noResultsRow = document.getElementById('no-materials-found-row');
            const backendEmptyRow = document.getElementById('no-materials-backend');

            // Helper om tekst te herleiden naar basisvorm
            function normalizeText(text) {
                if (!text) return '';
                return text.toLowerCase()
                    .normalize("NFD").replace(/[\u0300-\u036f]/g, "") // é -> e, à -> a
                    .replace(/[^a-z0-9]/g, ' ')                      // Speciale tekens naar spaties
                    .replace(/\s+/g, ' ')                             // Dubbele spaties weg
                    .trim();
            }

            // Levenshtein-distantie voor typfouten
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
                                matrix[i - 1][j - 1] + 1, // vervanging
                                matrix[i][j - 1] + 1,     // invoeging
                                matrix[i - 1][j] + 1      // verwijdering
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

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const query = normalizeText(this.value);
                    let visibleCount = 0;

                    if (query === '') {
                        tableRows.forEach(row => row.style.display = '');
                        if (noResultsRow) noResultsRow.classList.add('hidden');
                        if (backendEmptyRow) backendEmptyRow.style.display = '';
                        return;
                    }

                    // Schakel de database lege melding uit tijdens het zoeken
                    if (backendEmptyRow) backendEmptyRow.style.display = 'none';

                    const flatQuery = query.replace(/ /g, '');

                    tableRows.forEach(row => {
                        const nameEl = row.querySelector('.material-name');
                        const categoryEl = row.querySelector('.material-category');

                        const name = normalizeText(nameEl ? nameEl.textContent : '');
                        const category = normalizeText(categoryEl ? categoryEl.textContent : '');
                        
                        const flatName = name.replace(/ /g, '');
                        const flatCategory = category.replace(/ /g, '');

                        let isMatch = false;

                        // 1. Directe of spatieloze match (zoekt zowel in Naam als in Categorie)
                        if (name.includes(query) || category.includes(query) || 
                            flatName.includes(flatQuery) || flatCategory.includes(flatQuery)) {
                            isMatch = true;
                        }

                        // 2. Fuzzy logica voor typfouten (vanaf 3 letters)
                        if (!isMatch && flatQuery.length >= 3) {
                            const allowedDistance = getAllowedDistance(flatQuery);
                            
                            if (levenshtein(flatQuery, flatName) <= allowedDistance || 
                                levenshtein(flatQuery, flatCategory) <= allowedDistance) {
                                isMatch = true;
                            }

                            // Losse woorden checken
                            if (!isMatch) {
                                const queryWords = query.split(' ');
                                const nameWords = name.split(' ');

                                queryWords.forEach(qWord => {
                                    nameWords.forEach(nWord => {
                                        if (nWord.includes(qWord) || qWord.includes(nWord)) {
                                            isMatch = true;
                                        } else if (qWord.length >= 3 && levenshtein(qWord, nWord) <= getAllowedDistance(qWord)) {
                                            isMatch = true;
                                        }
                                    });
                                });
                            }
                        }

                        // Update de display status
                        if (isMatch) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Toon melding bij 0 resultaten
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