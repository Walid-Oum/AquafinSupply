<x-app-layout>
    <div class="p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Nieuw ticket</h1>
            <p class="text-gray-600">Maak een ticket aan om een probleem te melden.</p>
        </div>

        <div class="rounded-lg bg-white p-6 shadow">
            <form method="POST" action="{{ route('tickets.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="subject" class="block text-sm font-medium text-gray-700">
                        Onderwerp
                    </label>

                    <input
                        id="subject"
                        name="subject"
                        type="text"
                        value="{{ old('subject') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    >

                    @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">
                        Beschrijving
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    >{{ old('description') }}</textarea>

                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button
                        type="submit"
                        class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                    >
                        Ticket aanmaken
                    </button>

                    <a
                        href="{{ route('tickets.index') }}"
                        class="rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300"
                    >
                        Annuleren
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
