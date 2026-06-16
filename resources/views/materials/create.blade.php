<x-app-layout>
    <x-page-header title="Nieuw materiaal toevoegen" />
    {{ $riskLevels->count() }}

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
              <select
    name="category"
    class="w-full border rounded px-3 py-2"
    required>

    <option value="">
        Kies een categorie
    </option>

    @foreach($categories as $category)
        <option
            value="{{ $category }}"
            {{ old('category') == $category ? 'selected' : '' }}>
            {{ $category }}
        </option>
    @endforeach

</select>
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

                <label class="block font-bold mb-2">
                    Afbeelding
                </label>

                <input
                    type="file"
                    name="image"
                    class="w-full border rounded px-3 py-2">

                <p class="text-sm text-gray-500 mt-1">
                    Toegestane formaten: JPEG, PNG, JPG. Max 2MB.
                </p>

            </div>

            <div class="mb-4">

                <label class="block font-bold mb-2">
                    Risiconiveaus
                </label>
                @foreach($riskLevels as $riskLevel)

                    <pre>
        {{ $riskLevel->id }}
                        {{ $riskLevel->name }}
    </pre>

                    <label class="flex items-center gap-2 mb-2">

                        <input
                            type="checkbox"
                            name="risk_levels[]"
                            value="{{ $riskLevel->id }}">

                        {{ $riskLevel->name }}

                    </label>

                @endforeach

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
