<x-app-layout>
    <x-page-header title="Materialen overzicht" />

    <!-- Zoekbalk -->
    <div class="mb-4">
        <form method="GET" action="{{ route('technician.materials.index') }}" class="flex gap-2">
            <input type="text" name="search" placeholder="Zoeken op naam..." value="{{ request('search') }}" class="border rounded px-3 py-2 w-64">
            <x-button>
    Zoek
</x-button>
        </form>
    </div>

    <!-- Filter en Sorteer -->
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
          <a href="{{ route('technician.materials.index') }}">
    <x-button type="button">
        Reset
    </x-button>
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
</x-app-layout>