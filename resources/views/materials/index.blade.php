<x-app-layout>
    <x-page-header title="Materialen overzicht" />

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('materials.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
            + Nieuw materiaal
        </a>

        <!-- Filter op categorie -->
        <form method="GET" action="{{ route('materials.index') }}" class="flex gap-2">
            <select name="category" class="border rounded px-3 py-2">
                <option value="">Alle categorieën</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                        {{ $category }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-500 text-white px-4 py-2 rounded">Filter</button>
            <a href="{{ route('materials.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded">Reset</a>
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
                        <th class="text-left">Minimum voorraad</th>
                        <th class="text-left">Vooraadstatus</th>
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
                        <td class="space-x-3">
                            <a href="{{ route('materials.show', $material->id) }}" class="text-blue-500">Bekijk</a>
                            <a href="{{ route('materials.edit', $material->id) }}" class="text-yellow-500">Bewerk</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Geen materialen gevonden.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-app-layout>
