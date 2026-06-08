<x-app-layout>

    <x-page-header title="Materiaal details" />

    <x-card>

       @if($material->image)

    <img
        src="{{ asset('storage/' . $material->image) }}"
        class="w-64 rounded-lg mb-6">

@else

    <div class="w-64 h-40 rounded-lg mb-6 bg-gray-100 flex items-center justify-center text-gray-500">

        Geen afbeelding

    </div>

@endif

        <div class="space-y-3">

            <p>
                <strong>Naam:</strong>
                {{ $material->name }}
            </p>

            <p>
                <strong>Categorie:</strong>
                {{ $material->category }}
            </p>

            <p>
                <strong>Beschrijving:</strong>
                {{ $material->description }}
            </p>

            <p>
                <strong>Voorraad:</strong>
                {{ $material->stock }}
            </p>

            <p>
                <strong>Minimum voorraad:</strong>
                {{ $material->minimum_stock }}
            </p>

        </div>

        <div class="mt-6">

            <a href="{{ route('magazijn.materials.index') }}">
                <x-button>
                    Terug
                </x-button>
            </a>

        </div>

    </x-card>

</x-app-layout>