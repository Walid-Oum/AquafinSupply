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
            
            <ul id="global-search-results" class="absolute z-50 w-72 bg-white border border-gray-200 rounded mt-1 shadow-xl hidden max-h-60 overflow-y-auto divide-y divide-gray-100">
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
                            Voorraad
                        </th>

                        <th class="text-left p-3">
                            Nieuw voorraad
                        </th>

                       <th class="text-center">
    Opslaan
</th>

<th class="text-center">
    Bekijk
</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($materials as $material)

                    <tr class="border-b">

                        <td class="p-3">

                            {{ $material->name }}

                        </td>

                        <td class="p-3">

                            {{ $material->category }}

                        </td>

                        <td class="p-3">

                            {{ $material->stock }}

                        </td>

                    <td class="text-center">

    <form
        action="{{ route('magazijn.materials.update', $material->id) }}"
        method="POST"
        class="flex items-center justify-center gap-2">

        @csrf
        @method('PATCH')

        <input
            type="number"
            name="stock"
            value="{{ $material->stock }}"
            class="border rounded-lg px-3 py-2 w-24">

</td>

<td class="text-center">

        <x-button>
            Opslaan
        </x-button>

    </form>

</td>

<td class="text-center">

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
                            colspan="5"
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