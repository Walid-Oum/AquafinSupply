<x-app-layout>
    <x-page-header title="Nieuw materiaal toevoegen" />

    <x-card>
        <form action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block font-bold mb-2">Naam *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-bold mb-2">Categorie *</label>
                <input type="text" name="category" value="{{ old('category') }}" class="w-full border rounded px-3 py-2" required>
                @error('category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-bold mb-2">Beschrijving</label>
                <textarea name="description" rows="4" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-bold mb-2">Afbeelding</label>
                <input type="file" name="image" class="w-full border rounded px-3 py-2">
                <p class="text-sm text-gray-500 mt-1">Toegestane formaten: JPEG, PNG, JPG. Max 2MB.</p>
                @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-bold mb-2">Voorraad *</label>
                <input type="number" name="stock" value="{{ old('stock', 0) }}" class="w-full border rounded px-3 py-2" required>
                @error('stock')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
<div class="mb-4">

<label class="block font-bold mb-2">

Minimum voorraad

</label>

<input
type="number"
name="minimum_stock"
value="0"
class="w-full border rounded px-3 py-2">

</div>
            <div class="flex justify-end">
                <a href="{{ route('materials.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Annuleren</a>
                <x-button>
    Opslaan
</x-button>
            </div>
        </form>
    </x-card>
</x-app-layout>