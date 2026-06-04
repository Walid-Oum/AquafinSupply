<x-app-layout>

    <x-page-header title="Bestelling Detail" />

    <x-card>

        <div class="space-y-4">

            <div>
                <strong>Bestelling ID:</strong>
                #{{ $order->id }}
            </div>

            <div>
                <strong>Technieker:</strong>
                {{ $order->user->name }}
            </div>

            <div>
                <strong>Besteld op:</strong>
                {{ $order->created_at->format('d/m/Y H:i') }}
            </div>

            <div>
                <strong>Leverdatum:</strong>
                {{ $order->delivery_date }}
            </div>

            <div>
                <strong>Status:</strong>
                {{ $order->status }}
            </div>

            <div>
                <strong>Opmerking:</strong>
                {{ $order->comment ?? 'Geen opmerking' }}
            </div>

        </div>

    </x-card>

    <div class="mt-6">

        <x-card>

            <h2 class="text-xl font-semibold mb-4">

                Bestelde materialen

            </h2>

            <table class="w-full">

                <thead>

                    <tr class="border-b">
                        <th class="text-left p-3">Materiaal</th>
                        <th class="text-left p-3">Hoeveelheid</th>
                    </tr>

                </thead>

                <tbody>

                    @foreach($order->items as $item)

                        <tr class="border-b">

                            <td class="p-3">
                                {{ $item->material->name }}
                            </td>

                            <td class="p-3">
                                {{ $item->quantity }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </x-card>
<div class="mt-6">

    <a href="{{ route('admin.orders.index') }}"
       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">

        ← Terug

    </a>

</div>
    </div>

</x-app-layout>