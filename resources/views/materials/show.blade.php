@php
    $categoryImages = [
        'Aquafin tools' => 'aquafintools.png',
        'Bevestigingsmateriaal' => 'bevestigingsmateriaal.png',
        'Gereedschap' => 'gereedschap.png',
        'PBM' => 'PBM.png',
        'Technisch onderhoud' => 'technischeonderhoud.png',
        'Verbruiksgoederen' => 'verbruiksgoederen.png',
    ];
@endphp
<x-app-layout>
    <x-page-header title="Materiaal detail" />

    <x-card>
        <div class="mb-4">
            <strong>Afbeelding:</strong><br>
           @if($material->image)
    <img
        src="{{ Storage::url($material->image) }}"
        class="w-full max-w-sm h-auto object-cover rounded mt-2">
@else
    <img
        src="{{ asset('images/' . ($categoryImages[$material->category] ?? 'sidebar-bg.jpg')) }}"
        class="w-full max-w-sm h-auto object-cover rounded mt-2"
        alt="{{ $material->category }}">
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

      <div class="flex justify-end gap-4">
            <a href="{{ route('materials.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Terug</a>
         <a href="{{ route('materials.edit', $material->id) }}">
        <x-button>
            Bewerk
        </x-button>
    </a>
        </div>
    </x-card>
</x-app-layout>