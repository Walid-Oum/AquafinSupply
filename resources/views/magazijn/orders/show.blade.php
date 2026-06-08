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
                        <th class="text-left">Materiaal</th>
                        <th class="text-left">Aantal</th>
                    </tr>

                </thead>

                <tbody>

                    @foreach($order->items as $item)

                        <tr>

                            <td class="py-2">
                                {{ $item->material->name }}
                            </td>

                            <td>
                                {{ $item->quantity }}
                            </td>

                        </tr>

                    @endforeach

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