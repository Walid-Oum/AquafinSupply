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

        <table class="w-full">

            <thead>

                <tr class="border-b">

                    <th class="text-left p-3">Materiaal</th>
                    <th class="text-left p-3">Aantal</th>
                    <th class="text-left p-3">Actie</th>

                </tr>

            </thead>

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

    </x-card>

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

</x-app-layout>