<x-app-layout>

    <x-page-header title="Voorraad overzicht"/>

    <div class="mb-4">

        <form
            method="GET"
            action="{{ route('magazijn.materials.index') }}"
            class="flex gap-2">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Zoeken op materiaal..."
                class="border rounded px-3 py-2 w-72">

            <x-button>

                Zoek

            </x-button>

        </form>

    </div>

    <x-card>

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead>

                    <tr>

                        <th class="text-left p-3">
                            Naam
                        </th>

                        <th class="text-left p-3">
                            Categorie
                        </th>

                        <th class="text-left p-3">
                            Voorraad
                        </th>

                        <th class="text-left p-3">
                            Nieuw voorraad
                        </th>

                        <th class="text-left p-3">
                            Actie
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($materials as $material)

                    <tr class="border-b">

                        <td class="p-3">

                            {{ $material->name }}

                        </td>

                        <td class="p-3">

                            {{ $material->category }}

                        </td>

                        <td class="p-3">

                            {{ $material->stock }}

                        </td>

                        <td class="p-3">

                            <form
                                action="{{ route('magazijn.materials.update',$material->id) }}"
                                method="POST"
                                class="flex gap-2">

                                @csrf

                                @method('PATCH')

                                <input
                                    type="number"
                                    name="stock"
                                    min="0"
                                    value="{{ $material->stock }}"
                                    class="border rounded px-3 py-2 w-24">

                        </td>

                        <td class="p-3">

                                <x-button>

                                    Opslaan

                                </x-button>

                            </form>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="5"
                            class="text-center p-5">

                            Geen materialen gevonden.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </x-card>

</x-app-layout>