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

                        @if($material->image)

                            <img
                                src="{{ Storage::url($material->image) }}"
                                class="w-32 h-32 object-cover rounded mb-3 mx-auto">

                        @else

                            <div class="w-32 h-32 bg-gray-100 rounded mb-3 mx-auto"></div>

                        @endif

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

<form
    action="{{ route('cart.add', $material->id) }}"
    method="POST"
    class="mt-4">

    @csrf

    <button
        type="submit"
        onclick="event.stopPropagation();"
        class="w-full
               bg-[#0F4C81]
               hover:bg-[#1E6BA8]
               text-white
               py-2
               rounded-lg">

        + Toevoegen

    </button>

</form>

</a>

                @endforeach

            </div>

        </div>

    @endif

   <div class="flex items-center gap-4 mb-6">

    <form method="GET"
          action="{{ route('technician.materials.index') }}">

        <input
            type="hidden"
            name="category"
            value="{{ request('category') }}">

        <select
            name="sort"
            onchange="this.form.submit()"
            class="border rounded px-3 py-2">

            <option value="">Sorteer op naam</option>

            <option value="asc"
                {{ request('sort') == 'asc' ? 'selected' : '' }}>
                A-Z
            </option>

            <option value="desc"
                {{ request('sort') == 'desc' ? 'selected' : '' }}>
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
            class="border rounded px-3 py-2 w-64">

        <ul id="global-search-results"
            class="absolute z-50 w-64 bg-white border border-gray-200 rounded mt-1 shadow-xl hidden max-h-60 overflow-y-auto divide-y divide-gray-100">
        </ul>

    </div>

</div>

<div class="mb-6 flex gap-3 flex-wrap">

    <a
        href="{{ route('technician.materials.index', [
            'sort' => request('sort')
        ]) }}"
        class="px-5 py-2 rounded-full
        {{ request('category') == null ? 'bg-[#0F4C81] text-white' : 'bg-gray-100 hover:bg-gray-200' }}">

        Alles

    </a>

    @foreach($categories as $category)

        <a
            href="{{ route('technician.materials.index', [
                'category' => $category,
                'sort' => request('sort')
            ]) }}"
            class="px-5 py-2 rounded-full
           {{ request('category') == $category ? 'bg-[#0F4C81] text-white' : 'bg-gray-100 hover:bg-gray-200' }}">

            {{ $category }}

        </a>

    @endforeach

</div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

    @forelse($materials as $material)

        <a
            href="{{ route('technician.materials.show', $material->id) }}"
            class="bg-white rounded-2xl shadow-md hover:shadow-xl transition overflow-hidden">

            @if($material->image)

                <img
                    src="{{ Storage::url($material->image) }}"
                    class="w-full h-48 object-cover">

            @else

                <div class="w-full h-48 bg-gray-100 flex items-center justify-center">

                    <span class="text-gray-400">
                        Geen afbeelding
                    </span>

                </div>

            @endif

            <div class="p-5">

                <p class="text-xs text-gray-400 uppercase mb-1">
                    {{ $material->category }}
                </p>

                <h3 class="text-xl font-bold text-gray-800 mb-3">
                    {{ $material->name }}
                </h3>

                <div class="flex justify-between items-center mb-4">

                    <span class="text-sm text-gray-500">
                        Voorraad
                    </span>

                    <span class="font-bold text-green-600">
                        {{ $material->stock }}
                    </span>

                </div>

              @if($material->is_active)

    <span class="inline-block bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full">

        Actief

    </span>

@else

    <span class="inline-block bg-red-100 text-red-700 text-xs px-3 py-1 rounded-full">

        Inactief

    </span>

@endif

<form
    action="{{ route('cart.add', $material->id) }}"
    method="POST"
    class="mt-5">

    @csrf

    <button
        type="submit"
        onclick="event.stopPropagation();"
        class="w-full
               bg-[#0F4C81]
               hover:bg-[#1E6BA8]
               text-white
               font-semibold
               py-3
               rounded-xl">

        + Toevoegen 

    </button>

</form>

</div>

</a>

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
