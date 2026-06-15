<x-app-layout>
    <div class="p-8">
        <div class="mb-6">
            <x-page-header title="Materialen overzicht" />
            <p class="text-gray-600">Doorzoek de voorraad, filter op categorie of sorteer de lijst.</p>
        </div>

        <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
            <form method="GET" action="{{ route('technician.materials.index') }}" class="flex flex-wrap gap-3 items-center">
                
                <div class="relative w-full sm:w-64">
                    <input 
                        type="text" 
                        name="search"
                        id="global-material-search" 
                        autocomplete="off" 
                        placeholder="Zoeken op naam..." 
                        value="{{ request('search') }}" 
                        class="border border-gray-300 rounded px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-[#0F4C81] text-black">
                    
                    <ul id="global-search-results" class="absolute z-50 w-full bg-white border border-gray-200 rounded mt-1 shadow-xl hidden max-h-60 overflow-y-auto divide-y divide-gray-100">
                    </ul>
                </div>
                
                <div class="w-full sm:w-auto">
                    <select name="category" class="border border-gray-300 rounded px-3 py-2 text-sm w-full focus:outline-none focus:ring-2 focus:ring-[#0F4C81] text-black">
                        <option value="">Alle categorieën</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="w-full sm:w-auto">
                    <select name="sort" class="border border-gray-300 rounded px-3 py-2 text-sm w-full focus:outline-none focus:ring-2 focus:ring-[#0F4C81] text-black">
                        <option value="">Sorteer op naam</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>A-Z</option>
                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Z-A</option>
                    </select>
                </div>
                
                <div class="flex gap-2 w-full sm:w-auto sm:ml-auto">
                    <x-button class="bg-[#0F4C81] hover:bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium transition w-full sm:w-auto">
                        Filter & Sorteer
                    </x-button>
                    
                    <a href="{{ route('technician.materials.index') }}"
                       class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-medium hover:bg-gray-300 transition text-center w-full sm:w-auto">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <x-card>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left p-3 text-sm font-semibold text-gray-700">Naam</th>
                            <th class="text-left p-3 text-sm font-semibold text-gray-700">Categorie</th>
                            <th class="text-left p-3 text-sm font-semibold text-gray-700">Voorraad</th>
                            <th class="text-left p-3 text-sm font-semibold text-gray-700">Status</th>
                            <th class="text-center p-3 text-sm font-semibold text-gray-700">Actie</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($materials as $material)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3 text-sm font-medium text-gray-900">{{ $material->name }}</td>
                            <td class="p-3 text-sm text-gray-600">{{ $material->category }}</td>
                            <td class="p-3 text-sm text-gray-600">{{ $material->stock }}</td>
                            <td class="p-3 text-sm">
                                @if($material->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Actief</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactief</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                <a href="{{ route('technician.materials.show', $material->id) }}">
                                    <x-button type="button" class="text-xs py-1 px-3">
                                        Bekijk
                                    </x-button>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center p-8 text-sm text-gray-500 italic">Geen materialen gevonden.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
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