<x-app-layout>
    <x-page-header title="Materialen overzicht" />
    @if($recommendedMaterials->count() > 0)

        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">

            <h2 class="text-lg font-bold text-green-700 mb-4">
                Aanbevolen materialen
                (op basis van overstromingsrisico)
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                @foreach($recommendedMaterials as $material)

                    <a
                        href="{{ route('technician.materials.show', $material->id) }}"
                        class="border rounded-lg p-4 bg-white hover:shadow">

                        <h3 class="font-semibold">
                            {{ $material->name }}
                        </h3>

                        <p class="text-sm text-gray-500">
                            {{ $material->category }}
                        </p>

                        <p class="text-sm mt-2">
                            Voorraad:
                            {{ $material->stock }}
                        </p>

                    </a>

                @endforeach

            </div>

        </div>

    @endif

    <div class="mb-4">
        <div class="relative w-full max-w-xs">
            <div class="relative">
                <input
                    type="text"
                    id="global-material-search"
                    autocomplete="off"
                    placeholder="Zoeken op naam..."
                    value="{{ request('search') }}"
                    class="border rounded px-3 py-2 w-64">
            </div>

            <ul id="global-search-results" class="absolute z-50 w-64 bg-white border border-gray-200 rounded mt-1 shadow-xl hidden max-h-60 overflow-y-auto divide-y divide-gray-100">
            </ul>
        </div>
    </div>

    <div class="mb-4">
        <form method="GET" action="{{ route('technician.materials.index') }}" class="flex gap-2">
            <select name="category" class="border rounded px-3 py-2">
                <option value="">Alle categorieën</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                        {{ $category }}
                    </option>
                @endforeach
            </select>

            <select name="sort" class="border rounded px-3 py-2">
                <option value="">Sorteer op naam</option>
                <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>A-Z</option>
                <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Z-A</option>
            </select>

            <x-button>
                Filter & Sorteer
            </x-button>
            <a href="{{ route('technician.materials.index') }}"
               class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                Reset
            </a>
        </form>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="text-left">Naam</th>
                        <th class="text-left">Categorie</th>
                        <th class="text-left">Voorraad</th>
                        <th class="text-left">Status</th>
                        <th class="text-center">Actie</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $material)
                    <tr>
                        <td>{{ $material->name }}</td>
                        <td>{{ $material->category }}</td>
                        <td>{{ $material->stock }}</td>
                        <td>
                            @if($material->is_active)
                                <span class="text-green-600">Actief</span>
                            @else
                                <span class="text-red-600">Inactief</span>
                            @endif
                        </td>
                        <td class="text-center p-3">
                            <a href="{{ route('technician.materials.show', $material->id) }}">
                                <x-button type="button">
                                    Bekijk
                                </x-button>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Geen materialen gevonden.</td>
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

                        // Technieker klikt -> gaat naar de specifieke technieker detailpagina
                        li.addEventListener('click', function() {
                            searchInput.value = item.name;
                            resultsList.classList.add('hidden');
                            window.location.href = `/technician/materials/${item.id}`;
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
