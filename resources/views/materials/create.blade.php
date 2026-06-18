{{--

Pagina: Nieuw materiaal toevoegen

Beschrijving:
Formulier voor het registreren van nieuw materiaal binnen het voorraadbeheersysteem.

Functionaliteiten:
- Aanmaken van nieuw materiaal
- Selecteren van een materiaalcategorie
- Toevoegen van een beschrijving
- Uploaden van een afbeelding
- Koppelen van risiconiveaus aan materiaal

--}}
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

                <label class="block font-bold mb-3">
                    Risiconiveaus
                </label>

                <div class="space-y-3">

                    @foreach($riskLevels as $riskLevel)

                        <label
                            class="flex items-center gap-3 rounded border border-gray-200 p-3 hover:bg-gray-50 cursor-pointer"
                        >

                            <input
                                type="checkbox"
                                name="risk_levels[]"
                                value="{{ $riskLevel->id }}"
                                class="h-4 w-4">

                            <span
                                class="
                    px-3 py-1 rounded-full text-xs font-semibold

                    @if($riskLevel->name === 'Hoog')
                        bg-red-100 text-red-700
                    @elseif($riskLevel->name === 'Gemiddeld')
                        bg-yellow-100 text-yellow-700
                    @else
                        bg-green-100 text-green-700
                    @endif
                    "
                            >
                    {{ $riskLevel->name }}
                </span>

                        </label>

                    @endforeach

                </div>

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
