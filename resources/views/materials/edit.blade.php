<x-app-layout>
    <x-page-header title="Materiaal bewerken" />


    <x-card>
        <form action="{{ route('materials.update', $material->id) }}" method="POST">
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
                <label class="block font-bold mb-2">Voorraad *</label>
                <input type="number" name="stock" value="{{ old('stock', $material->stock) }}" class="w-full border rounded px-3 py-2" required>
                @error('stock')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <a href="{{ route('materials.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Annuleren</a>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Bijwerken</button>
            </div>
        </form>
    </x-card>
</x-app-layout>