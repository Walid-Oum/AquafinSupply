<x-app-layout>

    <x-page-header title="Materiaal bewerken" />

    <x-card>

        <form
            action="{{ route('materials.update', $material->id) }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-bold mb-2">
                    Naam *
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $material->name) }}"
                    class="w-full border rounded px-3 py-2"
                    required>

                @error('name')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-bold mb-2">
                    Categorie *
                </label>

                <input
                    type="text"
                    name="category"
                    value="{{ old('category', $material->category) }}"
                    class="w-full border rounded px-3 py-2"
                    required>

                @error('category')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block font-bold mb-2">
                    Beschrijving
                </label>

                <textarea
                    name="description"
                    rows="4"
                    class="w-full border rounded px-3 py-2">{{ old('description', $material->description) }}</textarea>

                @error('description')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="mb-4">

                <label class="block font-bold mb-2">
                    Huidige afbeelding
                </label>

                @if($material->image)

                    <img
                        id="materialImage"
                        src="{{ Storage::url($material->image) }}"
                        class="w-32 h-32 object-cover rounded">

                @else

                    <p class="text-gray-500">
                        Geen afbeelding
                    </p>

                @endif
                @if($material->image)

                    <div
                        id="deleteButtonContainer"
                        class="mt-3">

                        <button
                            type="button"
                            onclick="openDeleteModal()"
                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            Afbeelding verwijderen
                        </button>

                        <input
                            type="hidden"
                            name="remove_image"
                            id="remove_image"
                            value="0">

                    </div>

                @endif
            </div>

            <div class="mb-4">

                <label class="block font-bold mb-2">
                    Nieuwe afbeelding
                </label>

                <input
                    type="file"
                    name="image"
                    class="w-full border rounded px-3 py-2">

            </div>

            <div class="mb-4">

                <label class="block font-bold mb-2">
                    Voorraad *
                </label>

                <input
                    type="number"
                    name="stock"
                    min="0"
                    value="{{ old('stock', $material->stock) }}"
                    class="w-full border rounded px-3 py-2"
                    required>

            </div>

            <div class="mb-4">

                <label class="block font-bold mb-2">
                    Minimum voorraad *
                </label>

                <input
                    type="number"
                    name="minimum_stock"
                    min="0"
                    value="{{ old('minimum_stock', $material->minimum_stock) }}"
                    class="w-full border rounded px-3 py-2"
                    required>

            </div>

            <div class="mb-6">

                <label class="block font-bold mb-2">
                    Status *
                </label>

                <select
                    name="is_active"
                    class="w-full border rounded px-3 py-2">

                    <option
                        value="1"
                        {{ $material->is_active ? 'selected' : '' }}>

                        Actief

                    </option>

                    <option
                        value="0"
                        {{ !$material->is_active ? 'selected' : '' }}>

                        Inactief

                    </option>

                </select>

            </div>

            <div class="flex gap-3">

                <a
                    href="{{ route('materials.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded">

                    Annuleren

                </a>

               <x-button>
    Bijwerken
</x-button>

            </div>

        </form>

        <div class="mt-6 border-t pt-6">

            <form
                action="{{ route('materials.destroy', $material->id) }}"
                method="POST"
                onsubmit="return confirm('Weet je zeker dat je dit materiaal wilt verwijderen?')">

                @csrf
                @method('DELETE')

               <x-button>
    Verwijderen
</x-button>

            </form>

        </div>

    </x-card>
    <script>

        function openDeleteModal()
        {
            document
                .getElementById('deleteImageModal')
                .classList.remove('hidden');

            document
                .getElementById('deleteImageModal')
                .classList.add('flex');
        }

        function closeDeleteModal()
        {
            document
                .getElementById('deleteImageModal')
                .classList.add('hidden');

            document
                .getElementById('deleteImageModal')
                .classList.remove('flex');
        }
        function confirmDeleteImage()
        {
            document
                .getElementById('remove_image')
                .value = 1;

            closeDeleteModal();

            const image =
                document.getElementById('materialImage');

            if (image) {
                image.remove();
            }

            const buttonContainer =
                document.getElementById('deleteButtonContainer');

            if (buttonContainer) {
                buttonContainer.remove();
            }

            const label =
                document.querySelector('label.block.font-bold.mb-2');

            if (label &&
                label.textContent.includes('Huidige afbeelding'))
            {
                label.nextElementSibling?.remove();
            }
        }
    </script>

    <div
        id="deleteImageModal"
        class="fixed inset-0 bg-white bg-opacity-40 backdrop-blur-sm hidden items-center justify-center z-50">

        <div class="bg-white rounded-lg shadow-lg p-6 w-96">

            <h2 class="text-xl font-bold mb-4">
                Afbeelding verwijderen
            </h2>

            <p class="mb-6">
                Ben je zeker dat je deze afbeelding wilt verwijderen?
            </p>

            <div class="flex justify-end gap-3">

                <button
                    type="button"
                    onclick="closeDeleteModal()"
                    class="bg-gray-500 text-white px-4 py-2 rounded">

                    Annuleren

                </button>

                <button
                    type="button"
                    onclick="confirmDeleteImage()"
                    class="bg-red-600 text-white px-4 py-2 rounded">

                    Verwijderen

                </button>

            </div>

        </div>

    </div>

</x-app-layout>
