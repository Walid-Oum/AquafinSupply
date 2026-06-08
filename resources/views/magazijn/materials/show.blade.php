<x-app-layout>

    <x-page-header title="Materiaal details" />

    <x-card>

        @if($material->photo)
            <img
                src="{{ asset('storage/' . $material->photo) }}"
                class="w-64 rounded-lg mb-6">
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