<x-app-layout>

    <x-page-header title="Alle Bestellingen" />

    <form
        method="GET"
        action="{{ route('admin.orders.index') }}"
        class="mb-4">

        <select
            name="status"
            class="border rounded px-3 py-2">

            <option value="all">
                Alle statussen
            </option>

            <option value="Nieuw">
                Nieuw
            </option>

            <option value="In voorbereiding">
                In voorbereiding
            </option>

            <option value="Klaar voor levering">
               Klaar om af te halen
            </option>

            <option value="Geleverd">
                Afgehaald
            </option>

        </select>

        <x-button>
    Filter
</x-button>

    </form>

    <x-card>
        <table class="w-full">

            <thead>

                <tr class="border-b">

                    <th class="p-3 text-left">
                        ID
                    </th>

                    <th class="p-3 text-left">
                        Technieker
                    </th>

                    <th class="p-3 text-left">
                        Besteld op
                    </th>

                    <th class="p-3 text-left">
                        Leverdatum
                    </th>

                    <th class="p-3 text-left">
                        Status
                    </th>

                    <th class="p-3 text-left">
                        Actie
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
                            {{ $order->created_at->format('d/m/Y') }}
                        </td>

                        <td class="p-3">
                            {{ $order->delivery_date }}
                        </td>

                        <td class="p-3">
                            {{ $order->status }}
                        </td>

                        <td class="p-3">

                            <a
                                href="{{ route('admin.orders.show', $order->id) }}"
                                ><x-button> Bekijken</x-button>

                              

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="text-center p-6">

                            Geen bestellingen gevonden.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </x-card>

</x-app-layout>
