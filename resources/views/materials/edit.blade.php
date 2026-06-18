<x-app-layout>
    <div class="mx-auto w-full max-w-4xl space-y-6">
        <x-page-header title="Materiaal bewerken" />

        <x-card>
            <form
                action="{{ route('materials.update', $material->id) }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="mb-2 block font-bold">
                        Naam *
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $material->name) }}"
                        class="w-full rounded border px-3 py-2"
                        required
                    >

                    @error('name')
                    <p class="mt-1 text-sm text-red-500">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="mb-2 block font-bold">
                        Categorie *
                    </label>

                    <select
                        name="category"
                        class="w-full rounded border px-3 py-2"
                        required
                    >
                        @foreach($categories as $category)
                            <option
                                value="{{ $category }}"
                                {{ old('category', $material->category) == $category ? 'selected' : '' }}
                            >
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>

                    @error('category')
                    <p class="mt-1 text-sm text-red-500">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="mb-2 block font-bold">
                        Beschrijving
                    </label>

                    <textarea
                        name="description"
                        rows="4"
                        class="w-full rounded border px-3 py-2"
                    >{{ old('description', $material->description) }}</textarea>

                    @error('description')
                    <p class="mt-1 text-sm text-red-500">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="mb-2 block font-bold">
                        Huidige afbeelding
                    </label>

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

                    @if($material->image)
                        <img
                            id="materialImage"
                            src="{{ Storage::url($material->image) }}"
                            class="h-32 w-32 rounded object-cover"
                            alt="{{ $material->name }}"
                        >
                    @else
                        <img
                            src="{{ asset('images/' . ($categoryImages[$material->category] ?? 'sidebar-bg.jpg')) }}"
                            class="h-32 w-32 rounded object-cover"
                            alt="{{ $material->category }}"
                        >
                    @endif

                    @if($material->image)
                        <input
                            type="hidden"
                            name="remove_image"
                            id="remove_image"
                            value="0"
                        >

                        <div id="deleteButtonContainer" class="mt-3">
                            <button
                                type="button"
                                onclick="openDeleteModal()"
                                class="inline-flex w-full items-center justify-center rounded bg-red-600 px-4 py-2 text-white transition hover:bg-red-700 sm:w-auto"
                            >
                                Afbeelding verwijderen
                            </button>
                        </div>
                    @endif
                </div>

                <div class="mb-4">
                    <label class="mb-2 block font-bold">
                        Nieuwe afbeelding
                    </label>

                    <input
                        type="file"
                        name="image"
                        class="w-full rounded border px-3 py-2"
                    >
                </div>

                <div class="mb-4">
                    <label class="mb-2 block font-bold">
                        Risiconiveaus
                    </label>

                    <div class="space-y-3">
                        @foreach($riskLevels as $riskLevel)
                            <label class="flex cursor-pointer items-center gap-3 rounded border border-gray-200 p-3 hover:bg-gray-50">
                                <input
                                    type="checkbox"
                                    name="risk_levels[]"
                                    value="{{ $riskLevel->id }}"
                                    class="h-4 w-4"
                                    {{ $material->riskLevels->contains($riskLevel->id) ? 'checked' : '' }}
                                >

                                <span
                                    class="rounded-full px-3 py-1 text-xs font-semibold
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

                <div class="mb-6">
                    <label class="mb-2 block font-bold">
                        Status *
                    </label>

                    <select
                        name="is_active"
                        class="w-full rounded border px-3 py-2"
                    >
                        <option
                            value="1"
                            {{ $material->is_active ? 'selected' : '' }}
                        >
                            Actief
                        </option>

                        <option
                            value="0"
                            {{ !$material->is_active ? 'selected' : '' }}
                        >
                            Inactief
                        </option>
                    </select>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('materials.index') }}"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-gray-500 px-5 py-3 font-semibold text-white transition hover:bg-gray-600 sm:w-auto"
                    >
                        Annuleren
                    </a>

                    <x-button type="submit" class="w-full justify-center sm:w-auto">
                        Bijwerken
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>

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

            console.log(document.getElementById('remove_image').value);

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

            if (
                label &&
                label.textContent.includes('Huidige afbeelding')
            ) {
                label.nextElementSibling?.remove();
            }
        }
    </script>

    <div
        id="deleteImageModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-white bg-opacity-40 p-4 backdrop-blur-sm"
    >
        <div class="w-full max-w-sm rounded-lg bg-white p-6 shadow-lg">
            <h2 class="mb-4 text-xl font-bold">
                Afbeelding verwijderen
            </h2>

            <p class="mb-6">
                Ben je zeker dat je deze afbeelding wilt verwijderen?
            </p>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    onclick="closeDeleteModal()"
                    class="inline-flex w-full items-center justify-center rounded bg-gray-500 px-4 py-2 text-white transition hover:bg-gray-600 sm:w-auto"
                >
                    Annuleren
                </button>

                <button
                    type="button"
                    onclick="confirmDeleteImage()"
                    class="inline-flex w-full items-center justify-center rounded bg-red-600 px-4 py-2 text-white transition hover:bg-red-700 sm:w-auto"
                >
                    Verwijderen
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
