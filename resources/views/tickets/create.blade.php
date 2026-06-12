<x-app-layout>
    <div class="p-8">
        <div class="mb-6">
            <x-page-header title="Nieuw ticket" />
            <p class="mt-1 text-gray-600">
                Meld een probleem over een bestelling zodat het magazijn dit kan opvolgen.
            </p>
        </div>

        <div class="max-w-3xl rounded-xl bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('tickets.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="order_id" class="mb-1 block text-sm font-semibold text-gray-700">
                        Bestelling
                    </label>

                    <select
                        id="order_id"
                        name="order_id"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                        <option value="">Kies een bestelling</option>

                        @foreach ($orders as $order)
                            <option value="{{ $order->id }}" @selected(old('order_id') == $order->id)>
                                Bestelling #{{ $order->id }} — levering {{ $order->delivery_date }}
                            </option>
                        @endforeach
                    </select>

                    @error('order_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subject" class="mb-1 block text-sm font-semibold text-gray-700">
                        Onderwerp
                    </label>

                    <input
                        id="subject"
                        name="subject"
                        type="text"
                        value="{{ old('subject') }}"
                        placeholder="Bijvoorbeeld: materiaal ontbreekt"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >

                    @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="mb-1 block text-sm font-semibold text-gray-700">
                        Beschrijving
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        placeholder="Beschrijf kort wat het probleem is."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >{{ old('description') }}</textarea>

                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                   <x-button>
    Supportaanvraag aanmaken
</x-button>

                    <a
                        href="{{ route('tickets.index') }}"
                        class="rounded-lg bg-gray-100 px-5 py-2.5 font-medium text-gray-700 hover:bg-gray-200"
                    >
                        Annuleren
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
