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

    <x-status-badge
        :status="$order->status" />

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
        <th class="text-left p-3">Afbeelding</th>
        <th class="text-left p-3">Materiaal</th>
        <th class="text-left p-3">Hoeveelheid</th>
    </tr>
</thead>

<tbody>
    @foreach($order->items as $item)
        <tr class="border-b">
            <td class="p-3">
                @if($item->material->image)
                    <img src="{{ asset('storage/' . $item->material->image) }}" class="w-16 h-16 object-cover rounded-lg">
                @else
                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center text-xs text-gray-500">
                        Geen afbeelding
                    </div>
                @endif
            </td>
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

  <a href="{{ route('admin.orders.index') }}">
    <x-button>
        ← Terug
    </x-button>
</a>

</div>
    </div>

</x-app-layout>