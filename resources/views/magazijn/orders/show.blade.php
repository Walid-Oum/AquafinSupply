<x-app-layout>

    <x-page-header title="Bestelling details" />

    <x-card>

        <div class="space-y-3">

            <p>
                <strong>Bestelling:</strong>
                #{{ $order->id }}
            </p>

            <p>
                <strong>Technieker:</strong>
                {{ $order->user->name }}
            </p>

            <p>
                <strong>Leverdatum:</strong>
                {{ $order->delivery_date }}
            </p>

            <p>
                <strong>Status:</strong>
                <x-status-badge :status="$order->status"/>
            </p>

            <p>
                <strong>Opmerking:</strong>
                {{ $order->comment ?? '-' }}
            </p>

        </div>

        <div class="mt-8">

            <h2 class="text-xl font-bold mb-4">
                Materialen
            </h2>

            <table class="w-full">

               <thead>

    <tr>
        <th class="text-left">Foto</th>
        <th class="text-left">Materiaal</th>
        <th class="text-left">Aantal</th>
    </tr>

</thead>

<tbody>

    @forelse($order->items as $item)

        <tr>

            <td class="py-2">

                @if($item->material->image)

                    <img
                        src="{{ asset('storage/' . $item->material->image) }}"
                        class="w-16 h-16 object-cover rounded-lg">

                @else

                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center text-xs text-gray-500">

                        Geen afbeelding

                    </div>

                @endif

            </td>

            <td>
                {{ $item->material->name }}
            </td>

            <td>
                {{ $item->quantity }}
            </td>

        </tr>

    @empty

        <tr>

            <td colspan="3" class="text-center py-4 text-gray-500">

                Geen materialen gevonden.

            </td>

        </tr>

    @endforelse

</tbody>

            </table>

        </div>

       <div class="mt-6 flex gap-3">

    <a href="{{ route('magazijn.orders.edit', $order->id) }}">

        <x-button>

            Bewerk

        </x-button>

    </a>

    <a href="{{ route('magazijn.orders.index') }}">

        <x-button>

            Terug

        </x-button>

    </a>

</div>

    </x-card>

</x-app-layout>