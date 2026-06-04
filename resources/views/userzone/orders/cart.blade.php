{{-- 
    Pagina: Winkelmandje

    User Stories:
    US9 - Materiaal bestellen
    US10 - Leverdatum kiezen
    US11 - Meerdere materialen bestellen
--}}

<x-app-layout>

    <x-page-header title="Winkelmandje"/>

    <x-card>

        @if(session()->has('cart') && count(session()->get('cart')) > 0)

            <table class="w-full">

                <thead>

                    <tr class="border-b">

                        <th class="text-left p-3">Materiaal</th>
                        <th class="text-left p-3">Categorie</th>
                        <th class="text-left p-3">Aantal</th>
                        <th class="text-left p-3">Actie</th>

                    </tr>

<<<<<<< HEAD
           <tbody>

    @foreach($materials as $material)

        <tr class="border-b">

            <td class="p-3">

                {{ $material->name }}

            </td>

            <td class="p-3">

                <input
                    type="number"
                    value="1"
                    min="1"
                    class="border rounded-lg px-3 py-2 w-20">

            </td>

            <td class="p-3">

                Voorraad: {{ $material->stock }}

            </td>

        </tr>

    @endforeach

</tbody>

        </table>
=======
                </thead>

                <tbody>

                    @foreach(session()->get('cart') as $id => $item)

                        <tr class="border-b">

                            <td class="p-3">
                                {{ $item['name'] }}
                            </td>

                            <td class="p-3">
                                {{ $item['category'] }}
                            </td>

                            <td class="p-3">

                                <input
                                    type="number"
                                    value="{{ $item['quantity'] }}"
                                    min="1"
                                    class="border rounded-lg px-3 py-2 w-20">

                            </td>

                            <td class="p-3">

                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit">Verwijderen</x-button>
                                </form>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        @else

            <p class="text-gray-600 text-center py-4">Je winkelmandje is leeg.</p>

        @endif
>>>>>>> origin/main

    </x-card>

    @if(session()->has('cart') && count(session()->get('cart')) > 0)

        <div class="mt-6">

            <x-card>

                <div class="space-y-5">

                    <div>

                        <label class="block mb-2 font-semibold">

                            Leverdatum

                        </label>

                        <input
                            type="date"
                            class="w-full border rounded-lg px-4 py-3">

                    </div>

                    <div>

                        <label class="block mb-2 font-semibold">

                            Opmerking

                        </label>

                        <textarea
                            rows="4"
                            class="w-full border rounded-lg px-4 py-3"
                            placeholder="Extra informatie..."></textarea>

                    </div>

                    <div>

                        <x-button>

                            Bestelling plaatsen

                        </x-button>

                    </div>

                </div>

            </x-card>

        </div>

    @endif

</x-app-layout>