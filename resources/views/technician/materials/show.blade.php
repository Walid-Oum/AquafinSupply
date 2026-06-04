<x-app-layout>
    <x-page-header title="Materiaal detail" />

    <x-card>
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

        <div class="flex justify-end gap-2">
            <form action="{{ route('cart.add', $material->id) }}" method="POST">
                @csrf
               <button
    type="submit"
    class="inline-flex items-center gap-3
           bg-gradient-to-r from-blue-600 to-blue-700
           hover:from-blue-700 hover:to-blue-800
           text-white font-bold
           px-8 py-4 rounded-xl
           shadow-lg transition">

    🛒 Toevoegen aan winkelmandje

</button>
            </form>
            <a href="{{ route('technician.materials.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Terug</a>
        </div>
    </x-card>
</x-app-layout>