<x-app-layout>
    <x-page-header title="Materiaal detail" />

    <x-card>
        <div class="mb-4">
            <strong>Afbeelding:</strong><br>
            @if($material->image)
                <img src="{{ Storage::url($material->image) }}" class="w-48 h-48 object-cover rounded mt-2">
            @else
                <p class="text-gray-500 mt-1">Geen afbeelding</p>
            @endif
        </div>

        <div class="mb-4">
            <strong>Naam:</strong> {{ $material->name }}
        </div>
        <div class="mb-4">
            <strong>Categorie:</strong> {{ $material->category }}
        </div>
        <div class="mb-4">
            <strong>Beschrijving:</strong> {{ $material->description ?? 'Geen beschrijving' }}
        </div>
        <div class="mb-4">
            <strong>Voorraad:</strong> {{ $material->stock }}
        </div>
        <div class="mb-4">
            <strong>Status:</strong>
            @if($material->is_active)
                <span class="text-green-600">Actief</span>
            @else
                <span class="text-red-600">Inactief</span>
            @endif
        </div>

        <div class="flex justify-end">
            <a href="{{ route('materials.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Terug</a>
         <a href="{{ route('materials.edit', $material->id) }}">
        <x-button>
            Bewerk
        </x-button>
    </a>
        </div>
    </x-card>
</x-app-layout>