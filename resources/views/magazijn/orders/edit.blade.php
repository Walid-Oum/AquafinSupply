<x-app-layout>

    <x-page-header title="Bestelling wijzigen" />

    <x-card>

        <form
            action="{{ route('magazijn.orders.update', $order->id) }}"
            method="POST">

            @csrf
            @method('PATCH')

            <div class="space-y-5">

                <div>

                    <label class="block mb-2 font-semibold">

                        Technieker

                    </label>

                    <input
                        type="text"
                        value="{{ $order->user->name }}"
                        disabled
                        class="w-full border rounded-lg px-4 py-3 bg-gray-100">

                </div>

                <div>

                    <label class="block mb-2 font-semibold">

                        Leverdatum

                    </label>

                    <input
                        type="text"
                        value="{{ $order->delivery_date }}"
                        disabled
                        class="w-full border rounded-lg px-4 py-3 bg-gray-100">

                </div>

                <div>

                    <label class="block mb-2 font-semibold">

                        Status

                    </label>

                    <select
                        name="status"
                        class="w-full border rounded-lg px-4 py-3">

                        <option
                            value="Nieuw"
                            {{ $order->status == 'Nieuw' ? 'selected' : '' }}>

                            Nieuw

                        </option>

                        <option
                            value="In voorbereiding"
                            {{ $order->status == 'In voorbereiding' ? 'selected' : '' }}>

                            In voorbereiding

                        </option>

                        <option
                            value="Klaar om af te halen"
                            {{ $order->status == 'Klaar om af te halen' ? 'selected' : '' }}>

                            Klaar om af te halen

                        </option>

                        <option
                            value="Afgehaald"
                            {{ $order->status == 'Afgehaald' ? 'selected' : '' }}>

                            Afgehaald

                        </option>

                    </select>

                </div>

                <div class="mt-8">

                    <h2 class="text-xl font-bold mb-4">

                        Materialen

                    </h2>

                    @foreach($order->items as $item)

                        <div class="border rounded-lg p-4 mb-4">

                            <div class="flex justify-between items-center">

                                <div>

                                    <p class="font-semibold">

                                        {{ $item->material->name }}

                                    </p>

                                    <p class="text-gray-500 text-sm">

                                        Huidige aantal:
                                        {{ $item->quantity }}

                                    </p>

                                </div>

                                <div>

                                    <input
                                        type="number"
                                        min="0"
                                        name="quantities[{{ $item->id }}]"
                                        value="{{ $item->quantity }}"
                                        class="border rounded-lg px-3 py-2 w-24">

                                </div>

                            </div>

                            <p class="text-xs text-gray-500 mt-2">

                                Zet op 0 om dit materiaal uit de bestelling te verwijderen.

                            </p>

                        </div>

                    @endforeach

                </div>

                <div class="p-4 rounded-lg bg-yellow-100 border border-yellow-300">

                    <p class="font-semibold">

                        ⚠️ Waarschuwing

                    </p>

                    <p>

                        Het wijzigen van een bestelling kan gevolgen hebben voor de voorraad.

                    </p>

                </div>

                <div>

                    <label class="flex items-center gap-3">

                        <input
                            type="checkbox"
                            name="confirmation"
                            required>

                        Ik bevestig dat ik deze bestelling wil wijzigen.

                    </label>

                </div>

                <div class="flex gap-3">

                    <x-button>

                        Wijzigingen opslaan

                    </x-button>

                    <a
                        href="{{ route('magazijn.orders.show', $order->id) }}">

                        <x-button type="button">

                            Annuleren

                        </x-button>

                    </a>

                </div>

            </div>

        </form>

    </x-card>

</x-app-layout>