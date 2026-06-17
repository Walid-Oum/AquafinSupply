<x-app-layout>

    <x-page-header title="Voorraad overzicht"/>

    <div class="mb-4">
        <div class="relative w-full max-w-md">
            <div class="relative">
                <input
                    type="text"
                    id="global-material-search"
                    autocomplete="off"
                    placeholder="Zoeken op materiaal..."
                    class="border rounded px-3 py-2 w-72">
            </div>

            <ul id="global-search-results"
                class="absolute z-50 w-72 bg-white border border-gray-200 rounded mt-1 shadow-xl hidden max-h-60 overflow-y-auto divide-y divide-gray-100">
            </ul>
        </div>
    </div>

    <x-card>

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead>
                <tr>
                    <th class="text-left p-3">
                        Naam
                    </th>

                    <th class="text-left p-3">
                        Categorie
                    </th>

                    <th class="text-left p-3">
                        Voorraad in jouw depot
                    </th>

                    <th class="text-left p-3">
                        Nieuwe voorraad
                    </th>

                    <th class="text-center p-3">
                        Opslaan
                    </th>

                    <th class="text-center p-3">
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

                    <tr class="border-b">

                        <td class="p-3">
                            {{ $material->name }}
                        </td>

                        <td class="p-3">
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
                                    class="border rounded-lg px-3 py-2 w-24">
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

                    <tr>
                        <td
                            colspan="6"
                            class="text-center p-5">

                            Geen materialen gevonden.

                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </x-card>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('global-material-search');
            const resultsList = document.getElementById('global-search-results');
            const tableRows = document.querySelectorAll('tbody tr');

            // Helper om tekst te normaliseren voor lokale filtering in de tabel
            function normalizeText(text) {
                if (!text) return '';
                return text.toLowerCase()
                    .normalize("NFD").replace(/[\u0300-\u036f]/g, "") // Accenten weg (é -> e)
                    .replace(/[^a-z0-9]/g, ' ')                      // Speciale tekens weg
                    .replace(/\s+/g, ' ')                             // Dubbele spaties weg
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

            searchInput.addEventListener('input', async function () {
                const queryRaw = this.value;
                const queryClean = normalizeText(queryRaw);

                // --- DEEL 1: Live tabel filtering (Fuzzy) ---
                if (queryClean === '') {
                    tableRows.forEach(row => row.style.display = '');
                } else {
                    const flatQuery = queryClean.replace(/ /g, '');
                    
                    tableRows.forEach(row => {
                        const nameTd = row.querySelector('td:nth-child(1)');
                        const catTd = row.querySelector('td:nth-child(2)');
                        
                        if (!nameTd) return; // Sla eventuele lege rijen/colspan rijen over

                        const name = normalizeText(nameTd.textContent);
                        const category = normalizeText(catTd ? catTd.textContent : '');
                        
                        const flatName = name.replace(/ /g, '');
                        const flatCategory = category.replace(/ /g, '');

                        // Directe matches of spatieloze matches
                        if (name.includes(queryClean) || category.includes(queryClean) || 
                            flatName.includes(flatQuery) || flatCategory.includes(flatQuery)) {
                            row.style.display = '';
                            return;
                        }

                        // Fuzzy match op basis van Levenshtein
                        if (flatQuery.length >= 3) {
                            const allowedDistance = getAllowedDistance(flatQuery);
                            if (levenshtein(flatQuery, flatName) <= allowedDistance || 
                                levenshtein(flatQuery, flatCategory) <= allowedDistance) {
                                row.style.display = '';
                                return;
                            }
                        }

                        row.style.display = 'none';
                    });
                }

                // --- DEEL 2: Live Autocomplete suggestielijst via de API ---
                if (queryRaw.length < 2) {
                    resultsList.innerHTML = '';
                    resultsList.classList.add('hidden');
                    return;
                }

                try {
                    const response = await fetch(`/api/search-materials?q=${encodeURIComponent(queryRaw)}`);
                    const data = await response.json();

                    resultsList.innerHTML = '';

                    if (data.length > 0) {
                        resultsList.classList.remove('hidden');

                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer flex justify-between items-center text-sm';

                            li.innerHTML = `
                                <span class="font-medium text-gray-700">${item.name}</span>
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded">Stock: ${item.stock}</span>
                            `;

                            li.addEventListener('click', function () {
                                searchInput.value = item.name;
                                resultsList.classList.add('hidden');
                                window.location.href = `/magazijn/material/${item.id}`;
                            });

                            resultsList.appendChild(li);
                        });
                    } else {
                        resultsList.innerHTML = '<li class="px-4 py-2 text-sm text-gray-400 italic">Geen resultaten...</li>';
                        resultsList.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Fout bij ophalen suggesties:', error);
                }
            });

            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !resultsList.contains(e.target)) {
                    resultsList.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
