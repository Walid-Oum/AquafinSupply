<x-app-layout>
    <x-page-header title="Materialen overzicht" />

    @if($recommendedMaterials->count() > 0)

        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4">

            <div class="flex items-center justify-between mb-3">

                <h2 class="text-lg font-bold text-green-700">
                    Aanbevolen materialen
                    (op basis van overstromingsrisico)
                </h2>

                <button
                    type="button"
                    onclick="toggleRecommendations()"
                    id="recommendationIcon"
                    class="text-sm font-medium text-green-700 hover:text-green-900 hover:underline">

                    ▲ Verberg

                </button>

            </div>

            <div
                id="recommendationsContainer"
                class="mt-4">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">

                    @foreach($recommendedMaterials as $material)

                        <x-material-card
                            :material="$material"
                            :compact="true" />

                    @endforeach

                </div>

            </div>

        </div>

    @endif

    <div class="mb-6 flex items-center gap-4">
        <form method="GET" action="{{ route('technician.materials.index') }}">
            <input
                type="hidden"
                name="category"
                value="{{ request('category') }}"
            >

            <select
                name="sort"
                onchange="this.form.submit()"
                class="rounded border px-3 py-2"
            >
                <option value="">Sorteer op naam</option>

                <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>
                    A-Z
                </option>

                <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>
                    Z-A
                </option>
            </select>
        </form>

        <div class="relative w-full max-w-xs">
            <input
                type="text"
                id="global-material-search"
                autocomplete="off"
                placeholder="Zoeken op naam..."
                value="{{ request('search') }}"
                class="w-64 rounded border px-3 py-2"
            >

            <ul
                id="global-search-results"
                class="absolute z-50 mt-1 hidden max-h-60 w-64 divide-y divide-gray-100 overflow-y-auto rounded border border-gray-200 bg-white shadow-xl"
            >
            </ul>
        </div>
    </div>

    <div class="mb-6 flex flex-wrap gap-3">
        <a
            href="{{ route('technician.materials.index', [
                'sort' => request('sort')
            ]) }}"
            class="rounded-full px-5 py-2
            {{ request('category') == null ? 'bg-[#0F4C81] text-white' : 'bg-gray-100 hover:bg-gray-200' }}"
        >
            Alles
        </a>

        @foreach($categories as $category)
            <a
                href="{{ route('technician.materials.index', [
                    'category' => $category,
                    'sort' => request('sort')
                ]) }}"
                class="rounded-full px-5 py-2
                {{ request('category') == $category ? 'bg-[#0F4C81] text-white' : 'bg-gray-100 hover:bg-gray-200' }}"
            >
                {{ $category }}
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse($materials as $material)
            <x-material-card :material="$material" />
        @empty
            <div class="col-span-4 text-center text-gray-500">
                Geen materialen gevonden.
            </div>
        @endforelse
    </div>

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
        function toggleRecommendations()
        {
            const container =
                document.getElementById('recommendationsContainer');

            const icon =
                document.getElementById('recommendationIcon');

            if (container.classList.contains('hidden'))
            {
                container.classList.remove('hidden');
                icon.textContent = '▲ Verberg';
            }
            else
            {
                container.classList.add('hidden');
                icon.textContent = '▼ Toon aanbevelingen';
            }
        }
    </script>
</x-app-layout>
