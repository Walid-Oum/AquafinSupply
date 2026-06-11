<x-app-layout>

    <x-page-header title="Bestellingen overzicht" />

    <div class="mb-4">

        <form
            method="GET"
            action="{{ route('magazijn.orders.index') }}"
            class="flex gap-2">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Zoeken..."
                class="border rounded px-3 py-2 w-72">

            <x-button type="submit">

                Zoek

            </x-button>

            <a
                href="{{ route('magazijn.orders.index') }}"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">

                Reset

            </a>

        </form>

    </div>

    <x-card>

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead>

                <tr>

                    <th class="text-left p-3">
                        Nummer
                    </th>

                    <th class="text-left p-3">
                        Technieker
                    </th>

                    <th class="text-left p-3">
                        Depot/provincie
                    </th>

                    <th class="text-left p-3">
                        Leverdatum
                    </th>

                    <th class="text-left p-3">
                        Status
                    </th>

                    <th class="text-left p-3">
                        Actie
                    </th>

                    <th class="text-center p-3">
                        Bekijk
                    </th>

                </tr>

                </thead>

                <tbody>

                @forelse($orders as $order)

                    <tr class="border-b">

                        <td class="p-3">
                            #{{ $order->id }}
                        </td>

                        <td class="p-3">
                            {{ $order->user->name }}
                        </td>

                        <td class="p-3">
                            <div>
                                <p class="font-semibold">
                                    {{ $order->location->province ?? 'Geen provincie ingesteld' }}
                                </p>

                                <p class="text-sm text-gray-500">
                                    {{ $order->location->name ?? 'Geen depot gekoppeld' }}
                                </p>
                            </div>
                        </td>

                        <td class="p-3">
                            {{ $order->delivery_date }}
                        </td>

                        <td class="p-3">

                            <x-status-badge
                                :status="$order->status" />

                        </td>

                        <td class="p-3">

                            <form
                                action="{{ route('magazijn.orders.update', $order->id) }}"
                                method="POST"
                                class="flex gap-2">

                                @csrf
                                @method('PATCH')

                                <select
                                    name="status"
                                    class="border rounded px-3 py-2">

                                    <option value="Nieuw" @selected($order->status === 'Nieuw')>
                                        Nieuw
                                    </option>

                                    <option value="In voorbereiding" @selected($order->status === 'In voorbereiding')>
                                        In voorbereiding
                                    </option>

                                    <option value="Klaar om af te halen" @selected($order->status === 'Klaar om af te halen')>
                                        Klaar om af te halen
                                    </option>

                                    <option value="Afgehaald" @selected($order->status === 'Afgehaald')>
                                        Afgehaald
                                    </option>

                                </select>

                                <x-button>

                                    Opslaan

                                </x-button>

                            </form>

                        </td>

                        <td class="text-center p-3">

                            <a
                                href="{{ route('magazijn.orders.show', $order->id) }}"
                                class="font-semibold text-[#0F4C81] hover:underline">

                                Bekijk

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="text-center p-5">

                            Geen bestellingen gevonden.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </x-card>

</x-app-layout>
