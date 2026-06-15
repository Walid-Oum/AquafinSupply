<x-app-layout>
    <x-page-header title="Materialen overzicht" />

    <div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <a href="{{ route('materials.create') }}">
            <x-button>
                 + Nieuw materiaal
            </x-button>
        </a>

        <div class="flex flex-wrap gap-4 items-center w-full md:w-auto justify-end">
            <form method="GET" action="{{ route('materials.index') }}" class="flex gap-2 items-center relative">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif

                <div class="relative">
                    <input 
                        type="text" 
                        name="search"
                        id="global-material-search" 
                        value="{{ request('search') }}"
                        autocomplete="off" 
                        placeholder="Zoek op naam, categorie..." 
                        class="border rounded px-3 py-2 w-64 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F4C81] text-black">
                    
                    <ul id="global-search-results" class="absolute z-50 w-64 bg-white border border-gray-200 rounded mt-1 shadow-xl hidden max-h-60 overflow-y-auto divide-y divide-gray-100">
                    </ul>
                </div>

                <button type="submit" class="bg-[#0F4C81] hover:bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium transition">
                    Zoek
                </button>

                @if(request('search'))
                    <a href="{{ route('materials.index', request()->except('search')) }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-400 transition">
                        Wis
                    </a>
                @endif
            </form>

            <form method="GET" action="{{ route('materials.index') }}" class="flex gap-2">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <select name="category" class="border rounded px-3 py-2 text-sm text-black">
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
                @if(request('category') || request('search'))
                    <a href="{{ route('materials.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-400 transition flex items-center">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="text-left">Naam</th>
                        <th class="text-left">Categorie</th>
                        <th class="text-left">Voorraad</th>
                        <th class="text-left">Minimum voorraad</th>
                        <th class="text-left">Voorraadstatus</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Acties</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $material)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="font-medium text-gray-900">{{ $material->name }}</td>
                        <td>{{ $material->category }}</td>
                        <td>
                            @if($material->stock <= $material->minimum_stock)
                                <span class="text-red-600 font-bold">
                                    {{ $material->stock }}
                                </span>
                            @else
                                {{ $material->stock }}
                            @endif
                        </td>
                        <td>
                            {{ $material->minimum_stock }}
                        </td>

                        <td>
                            @if($material->stock <= $material->minimum_stock)
                                <span class="text-red-600 font-bold">
                                    Lage voorraad
                                </span>
                            @else
                                <span class="text-green-600">
                                    OK
                                </span>
                            @endif
                        </td>

                        <td>
                            @if($material->is_active)
                                <span class="text-green-600">Actief</span>
                            @else
                                <span class="text-red-600">Inactief</span>
                            @endif
                        </td>
                        <td class="space-x-3">
                            <a href="{{ route('materials.show', $material->id) }}" class="text-blue-500 hover:underline">Bekijk</a>
                            <a href="{{ route('materials.edit', $material->id) }}" class="text-yellow-500 hover:underline">Bewerk</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-gray-500 italic">Geen materialen gevonden.</td>
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
                // Maakt verbinding met de verbeterde searchSuggestions-methode in je controller
                const response = await fetch(`/api/search-materials?q=${encodeURIComponent(query)}`);
                const data = await response.json();

                resultsList.innerHTML = '';

                if (data.length > 0) {
                    resultsList.classList.remove('hidden');
                    
                    data.forEach(item => {
                        const li = document.createElement('li');
                        li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer flex justify-between items-center text-sm text-black';
                        
                        li.innerHTML = `
                            <span class="font-medium text-gray-700">${item.name}</span>
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded">Stock: ${item.stock}</span>
                        `;
                        
                        // Wanneer je op een suggestie klikt, filtert de tabel direct op dat item
                        li.addEventListener('click', function() {
                            searchInput.value = item.name;
                            resultsList.classList.add('hidden');
                            this.closest('form').submit(); // Verzendt het formulier automatisch
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