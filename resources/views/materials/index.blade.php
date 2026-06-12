<x-app-layout>
    <x-page-header title="Materialen overzicht" />

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
                
                <ul id="global-search-results" class="absolute z-50 w-64 bg-white border border-gray-200 rounded mt-1 shadow-xl hidden max-h-60 overflow-y-auto divide-y divide-gray-100">
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
                <a href="{{ route('materials.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">Reset</a>
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
                    <tr>
                        <td>{{ $material->name }}</td>
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
                      <td>
    <a
        href="{{ route('materials.show', $material->id) }}"
        class="text-blue-500 hover:underline">

        Bekijk

    </a>
</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Geen materialen gevonden.</td>
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
                        
                        // Gaat direct naar de reguliere admin-view van het materiaal
                        li.addEventListener('click', function() {
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