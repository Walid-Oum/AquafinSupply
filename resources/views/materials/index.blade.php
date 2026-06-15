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
                    placeholder="Snel zoeken op naam..."
                    class="border rounded px-3 py-2 w-64 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F4C81]">

                <ul id="global-search-results"
                    class="absolute z-50 w-64 bg-white border border-gray-200 rounded mt-1 shadow-xl hidden max-h-60 overflow-y-auto divide-y divide-gray-100">
                </ul>
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

                <x-button>
                    Filter
                </x-button>

                <a href="{{ route('materials.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">
                    Reset
                </a>
            </form>
        </div>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-[1100px] w-full text-sm">
                <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                        Naam
                    </th>

                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                        Categorie
                    </th>

                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                        Totale voorraad
                    </th>

                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                        Minimum per depot
                    </th>

                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                        Voorraadstatus
                    </th>

                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                        Status
                    </th>

                    <th class="px-4 py-3 text-left font-semibold text-gray-700">
                        Acties
                    </th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                @forelse($materials as $material)
                    @php
                        $totalStock = $material->stocks->sum('stock');

                        $minimumStock = $material->stocks->max('minimum_stock') ?? $material->minimum_stock;

                        $hasLowStock = $material->stocks->contains(function ($stock) {
                            return $stock->stock <= $stock->minimum_stock;
                        });
                    @endphp

                    <tr
    onclick="window.location='{{ route('materials.show', $material->id) }}'"
    class="cursor-pointer hover:bg-gray-100 transition">
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $material->name }}
                        </td>

                        <td class="px-4 py-3 text-gray-600">
                            {{ $material->category }}
                        </td>

                        <td class="px-4 py-3">
                            @if($hasLowStock)
                                <span class="text-red-600 font-bold">
                                    {{ $totalStock }}
                                </span>
                            @else
                                <span class="text-gray-800">
                                    {{ $totalStock }}
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-gray-700">
                            {{ $minimumStock }}
                        </td>

                        <td class="px-4 py-3">
                            @if($hasLowStock)
                                <span class="inline-block bg-red-100 text-red-700 text-xs font-semibold px-3 py-1 rounded-full">
                                    Lage voorraad in minstens één depot
                                </span>
                            @else
                                <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                                    OK
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            @if($material->is_active)
                                <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                                    Actief
                                </span>
                            @else
                                <span class="inline-block bg-red-100 text-red-700 text-xs font-semibold px-3 py-1 rounded-full">
                                    Inactief
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <a
    href="{{ route('materials.show', $material->id) }}"
    onclick="event.stopPropagation();">

    <x-button>
        Bekijk
    </x-button>

</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">
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

            searchInput.addEventListener('input', async function () {
                const query = this.value;

                if (query.length < 2) {
                    resultsList.innerHTML = '';
                    resultsList.classList.add('hidden');
                    return;
                }

                try {
                    const response = await fetch(`/api/search-materials?q=${encodeURIComponent(query)}`);
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
                                window.location.href = `/materials/${item.id}`;
                            });

                            resultsList.appendChild(li);
                        });
                    } else {
                        resultsList.innerHTML = '<li class="px-4 py-2 text-sm text-gray-400 italic">Geen resultaten...</li>';
                        resultsList.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Fout:', error);
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
