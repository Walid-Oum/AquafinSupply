{{--
    Pagina: Bestelling detail

    Doel:
    Toont de details van een geselecteerde bestelling
    zodat een administrator de bestelling kan raadplegen.

    Functionaliteiten:
    - Bekijken van bestelgegevens
    - Bekijken van techniekergegevens
    - Bekijken van bestelstatus
    - Bekijken van opmerking
    - Overzicht van bestelde materialen
    - Responsieve weergave voor desktop en mobiel

    Gebruikersrol:
    - Admin

    Opmerking:
    Indien een materiaal geen eigen afbeelding heeft,
    wordt automatisch een categorieafbeelding getoond.
--}}
@php
    $categoryImages = [
        'Aquafin tools' => 'aquafintools.png',
        'Bevestigingsmateriaal' => 'bevestigingsmateriaal.png',
        'Gereedschap' => 'gereedschap.png',
        'PBM' => 'PBM.png',
        'Technisch onderhoud' => 'technischeonderhoud.png',
        'Verbruiksgoederen' => 'verbruiksgoederen.png',
    ];
@endphp
<x-app-layout>

    <x-page-header title="Bestelling Detail" />
    {{-- Algemene bestelinformatie --}}
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
    {{-- Desktopweergave van bestelde materialen --}}
   <div class="hidden lg:block mt-6">

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

    <img
        src="{{ asset('storage/' . $item->material->image) }}"
        class="w-16 h-16 object-cover rounded-lg border">

@else

    <img
        src="{{ asset('images/' . ($categoryImages[$item->material->category] ?? 'sidebar-bg.jpg')) }}"
        class="w-16 h-16 object-cover rounded-lg border"
        alt="{{ $item->material->category }}">

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
        </div>
    {{-- Mobiele weergave van bestelde materialen --}}
   <div class="lg:hidden space-y-4 mt-8">

    @foreach($order->items as $item)

        <x-card>

            <div class="flex gap-4 items-center">

                @if($item->material->image)
                    <img
                        src="{{ asset('storage/' . $item->material->image) }}"
                        class="w-16 h-16 object-cover rounded-lg border">
                @else
                    <img
                        src="{{ asset('images/' . ($categoryImages[$item->material->category] ?? 'sidebar-bg.jpg')) }}"
                        class="w-16 h-16 object-cover rounded-lg border">
                @endif

                <div>
                    <p class="font-semibold">
                        {{ $item->material->name }}
                    </p>

                    <p class="text-sm text-gray-500">
                        Hoeveelheid: {{ $item->quantity }}
                    </p>
                </div>

            </div>

        </x-card>

    @endforeach

</div>
<div class="mt-6">

  <a href="{{ route('admin.orders.index') }}">
    <x-button>
        ← Terug
    </x-button>
</a>

</div>


</x-app-layout>
