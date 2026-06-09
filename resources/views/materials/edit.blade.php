<x-app-layout>
    <x-page-header title="Materiaal bewerken" />

    <x-card>
        <form action="{{ route('materials.update', $material->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-bold mb-2">Naam *</label>
                <input type="text" name="name" value="{{ old('name', $material->name) }}" class="w-full border rounded px-3 py-2" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-bold mb-2">Categorie *</label>
                <input type="text" name="category" value="{{ old('category', $material->category) }}" class="w-full border rounded px-3 py-2" required>
                @error('category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-bold mb-2">Beschrijving</label>
                <textarea name="description" rows="4" class="w-full border rounded px-3 py-2">{{ old('description', $material->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-bold mb-2">Huidige afbeelding</label>
                @if($material->image)
                    <div class="mt-1">
                        <img src="{{ Storage::url($material->image) }}" class="w-32 h-32 object-cover rounded">
                    </div>
                @else
                    <p class="text-gray-500">Geen afbeelding</p>
                @endif
            </div>

            <div class="mb-4">
                <label class="block font-bold mb-2">Nieuwe afbeelding (optioneel)</label>
                <input type="file" name="image" class="w-full border rounded px-3 py-2">
                <p class="text-sm text-gray-500 mt-1">Toegestane formaten: JPEG, PNG, JPG. Max 2MB.</p>
                @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-bold mb-2">Voorraad *</label>
                <input type="number" name="stock" min="0" value="{{ old('stock', $material->stock) }}" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block font-bold mb-2">Minimum voorraad *</label>
                <input type="number" name="minimum_stock" min="0" value="{{ old('minimum_stock', $material->minimum_stock) }}" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block font-bold mb-2">Status *</label>
                <select name="is_active" class="w-full border rounded px-3 py-2">
                    <option value="1" {{ $material->is_active ? 'selected' : '' }}>Actief</option>
                    <option value="0" {{ !$material->is_active ? 'selected' : '' }}>Inactief</option>
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <form action="{{ route('materials.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Weet je zeker dat je {{ $material->name }} wilt verwijderen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Verwijderen</button>
                </form>
                <a href="{{ route('materials.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Annuleren</a>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Bijwerken</button>
            </div>
        </form>
    </x-card>
</x-app-layout>