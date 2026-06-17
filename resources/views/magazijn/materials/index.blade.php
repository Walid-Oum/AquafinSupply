<x-app-layout>

    <x-page-header title="Voorraad overzicht"/>

    <div class="mb-4">
        <div class="relative w-full max-w-md">
            <div class="relative">
                <input
                    type="text"
                    id="global-material-search"
                    autocomplete="off"
                    placeholder="Zoeken op materiaal of categorie..."
                    class="border rounded px-3 py-2 w-72 focus:outline-none focus:ring-2 focus:ring-[#0F4C81]">
            </div>
        </div>
    </div>

    <x-card>

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left p-3 text-gray-700 font-semibold">
                        Naam
                    </th>

                    <th class="text-left p-3 text-gray-700 font-semibold">
                        Categorie
                    </th>

                    <th class="text-left p-3 text-gray-700 font-semibold">
                        Voorraad in jouw depot
                    </th>

                    <th class="text-left p-3 text-gray-700 font-semibold">
                        Nieuwe voorraad
                    </th>

                    <th class="text-center p-3 text-gray-700 font-semibold">
                        Opslaan
                    </th>

                    <th class="text-center p-3 text-gray-700 font-semibold">
                        Bekijk
                    </th>
                </tr>
                </thead>

                <tbody>

                @forelse($materials as $material)

                    @php
                        $localStock = $material->stocks->first();
                        $stock = $localStock?->stock ?? 0;
                        $minimumStock = $localStock?->minimum_stock ?? 0;
                    @endphp

                    <tr class="material-row border-b hover:bg-gray-50 transition-colors">

                        <td class="p-3 text-gray-800 font-medium material-name">
                            {{ $material->name }}
                        </td>

                        <td class="p-3 text-gray-600 material-category">
                            {{ $material->category }}
                        </td>

                        <td class="p-3">
                            @if($stock <= $minimumStock)
                                <span class="text-red-600 font-bold">
                                    {{ $stock }}
                                </span>
                            @else
                                <span class="text-green-600 font-semibold">
                                    {{ $stock }}
                                </span>
                            @endif
                        </td>

                        <td class="p-3 text-center">
                            <form
                                id="stock-form-{{ $material->id }}"
                                action="{{ route('magazijn.materials.update', $material->id) }}"
                                method="POST">

                                @csrf
                                @method('PATCH')

                                <input
                                    type="number"
                                    name="stock"
                                    value="{{ $stock }}"
                                    min="0"
                                    class="border rounded-lg px-3 py-1.5 w-24 text-center focus:outline-none focus:ring-1 focus:ring-[#0F4C81]">
                            </form>
                        </td>

                        <td class="p-3 text-center">
                            <x-button form="stock-form-{{ $material->id }}">
                                Opslaan
                            </x-button>
                        </td>

                        <td class="p-3 text-center">
                            <a
                                href="{{ route('magazijn.materials.show', $material->id) }}"
                                class="font-semibold text-[#0F4C81] hover:underline">
                                Bekijk
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr id="no-materials-backend">
                        <td colspan="6" class="text-center p-5 text-gray-500">
                            Geen materialen gevonden.
                        </td>
                    </tr>

                @endforelse

                <tr id="no-materials-found-row" class="hidden">
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic bg-gray-50">
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

            // Helper om tekst te normaliseren voor lokale filtering in de tabel
            function normalizeText(text) {
                if (!text) return '';
                return text.toLowerCase()
                    .normalize("NFD").replace(/[\u0300-\u036f]/g, "") 
                    .replace(/[^a-z0-9]/g, ' ')                      
                    .replace(/\s+/g, ' ')                             
                    .trim();
            }

            // Levenshtein afstand berekenen voor typfouten in de tabel
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

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const queryClean = normalizeText(this.value);
                    let visibleCount = 0;

                    if (queryClean === '') {
                        tableRows.forEach(row => row.style.display = '');
                        if (noResultsRow) noResultsRow.classList.add('hidden');
                        if (backendEmptyRow) backendEmptyRow.style.display = '';
                        return;
                    }

                    if (backendEmptyRow) backendEmptyRow.style.display = 'none';
                    const flatQuery = queryClean.replace(/ /g, '');
                    
                    tableRows.forEach(row => {
                        const nameEl = row.querySelector('.material-name');
                        const categoryEl = row.querySelector('.material-category');
                        
                        const name = normalizeText(nameEl ? nameEl.textContent : '');
                        const category = normalizeText(categoryEl ? categoryEl.textContent : '');
                        
                        const flatName = name.replace(/ /g, '');
                        const flatCategory = category.replace(/ /g, '');

                        let isMatch = false;

                        // 1. Directe of spatieloze match
                        if (name.includes(queryClean) || category.includes(queryClean) || 
                            flatName.includes(flatQuery) || flatCategory.includes(flatQuery)) {
                            isMatch = true;
                        }

                        // 2. Fuzzy match op basis van Levenshtein (vanaf 3 karakters)
                        if (!isMatch && flatQuery.length >= 3) {
                            const allowedDistance = getAllowedDistance(flatQuery);
                            if (levenshtein(flatQuery, flatName) <= allowedDistance || 
                                levenshtein(flatQuery, flatCategory) <= allowedDistance) {
                                isMatch = true;
                            }

                            // Woord-voor-woord controle voor complexere namen
                            if (!isMatch) {
                                const queryWords = queryClean.split(' ');
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

                        if (isMatch) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Toon of verberg de "Geen resultaten gevonden" melding
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