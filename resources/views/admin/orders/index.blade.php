<x-app-layout>

    <x-page-header title="Alle Bestellingen" />

   

 <div class="mb-3 flex flex-col lg:flex-row gap-4 lg:justify-end lg:items-center">
 <div class="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center">
        <div class="relative">
            <input
                type="text"
                id="global-order-search"
                autocomplete="off"
                placeholder="Bestelling zoeken..."
              class="border rounded px-3 py-2 w-full sm:w-64 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F4C81]">
     
</div>

       <form
    method="GET"
    action="{{ route('admin.orders.index') }}"
    class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">

            <select
                name="status"
                class="border rounded px-3 py-2 text-sm">

                <option value="all">Alle statussen</option>

                <option value="Nieuw" {{ request('status') == 'Nieuw' ? 'selected' : '' }}>
                    Nieuw
                </option>

                <option value="In voorbereiding" {{ request('status') == 'In voorbereiding' ? 'selected' : '' }}>
                    In voorbereiding
                </option>

                <option value="Klaar voor levering" {{ request('status') == 'Klaar voor levering' ? 'selected' : '' }}>
                    Klaar om af te halen
                </option>

                <option value="Geleverd" {{ request('status') == 'Geleverd' ? 'selected' : '' }}>
                    Afgehaald
                </option>

            </select>

            <select
                name="location_id"
                class="border rounded px-3 py-2 text-sm">

                <option value="">
                    Alle depots
                </option>

                @foreach($locations as $location)

                    <option
                        value="{{ $location->id }}"
                        {{ request('location_id') == $location->id ? 'selected' : '' }}>

                        {{ $location->name }}

                    </option>

                @endforeach

            </select>

            <x-button>
                Filter
            </x-button>

            <a
                href="{{ route('admin.orders.index') }}"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-400 transition">

                Reset

            </a>

        </form>

    </div>

</div>
   <div class="hidden lg:block">
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

                    <tr class="order-row border-b">

                        <td class="p-3 order-id">
                            #{{ $order->id }}
                        </td>

                       <td class="p-3 order-technician">
                            {{ $order->user->name }}
                        </td>

                        <td class="p-3">
                            {{ $order->created_at->format('d/m/Y') }}
                        </td>

                        <td class="p-3">
                            {{ $order->delivery_date }}
                        </td>

                    <td class="p-3 order-status">
    <span class="hidden">{{ $order->status }}</span>
    <x-status-badge :status="$order->status" />
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
</div>
    <div class="lg:hidden space-y-4">

    @forelse($orders as $order)

        <x-card>

            <div class="space-y-2">

                <p>
                    <strong>ID:</strong>
                    #{{ $order->id }}
                </p>

                <p>
                    <strong>Technieker:</strong>
                    {{ $order->user->name }}
                </p>

                <p>
                    <strong>Besteld op:</strong>
                    {{ $order->created_at->format('d/m/Y') }}
                </p>

                <p>
                    <strong>Leverdatum:</strong>
                    {{ $order->delivery_date }}
                </p>

                <div>
                    <strong>Status:</strong>
                    <x-status-badge :status="$order->status" />
                </div>

                <div class="pt-2">
                    <a href="{{ route('admin.orders.show', $order->id) }}">
                        <x-button>
                            Bekijken
                        </x-button>
                    </a>
                </div>

            </div>

        </x-card>

    @empty

        <x-card>
            Geen bestellingen gevonden.
        </x-card>

    @endforelse

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('global-order-search');
    const rows = document.querySelectorAll('.order-row');

    searchInput.addEventListener('input', function () {

        const query = this.value.toLowerCase();

        rows.forEach(row => {

            const id =
                row.querySelector('.order-id')?.textContent.toLowerCase() || '';

            const technician =
                row.querySelector('.order-technician')?.textContent.toLowerCase() || '';

            const status =
                row.querySelector('.order-status')?.textContent.toLowerCase() || '';

            if (
                id.includes(query) ||
                technician.includes(query) ||
                status.includes(query)
            ) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }

        });

    });

});
</script>
</x-app-layout>
