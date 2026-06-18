{{--
    MAGAZIJN - BESTELLING DETAILS

    @author
    @version     1.0
    @since       2026-06-18

    Deze view toont alle details van een specifieke bestelling voor
    magazijnmedewerkers. Hier worden bestelgegevens getoond zoals:
    bestellingnummer, technieker, leverdatum, depot, stad en opmerking.
    Ook worden alle materialen in de bestelling weergegeven met afbeelding,
    categorie en hoeveelheid. De view bevat een mobiele en desktop layout.

    @see App\Http\Controllers\Userzone\OrderController::warehouseShow()
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
    <div class="space-y-6">
        {{-- HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <x-page-header title="Bestelling details" />

                <p class="mt-1 text-sm text-gray-600 sm:text-base">
                    Bekijk de gegevens en materialen van deze bestelling.
                </p>
            </div>

            <a
                href="{{ route('magazijn.orders.index') }}"
                class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
            >
                ← Terug
            </a>
        </div>

        {{-- BESTELGEGEVENS --}}
        <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-5 flex flex-col gap-3 border-b border-gray-100 pb-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-gray-400">
                        Bestelling
                    </p>

                    <h2 class="mt-1 text-2xl font-bold text-[#0F4C81]">
                        #{{ $order->id }}
                    </h2>
                </div>

                <x-status-badge :status="$order->status" />
            </div>

            <div class="grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm font-semibold text-gray-500">
                        Technieker
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        {{ $order->user?->name ?? 'Onbekend' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-500">
                        Leverdatum
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        {{ $order->delivery_date ?? 'Geen leverdatum' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-500">
                        Depot/provincie
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        {{ $order->location?->province ?? 'Geen provincie ingesteld' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-500">
                        Depot
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        {{ $order->location?->name ?? 'Geen depot gekoppeld' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-500">
                        Stad
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        {{ $order->location?->city ?? 'Geen stad gekoppeld' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-500">
                        Opmerking
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        {{ $order->comment ?? '-' }}
                    </p>
                </div>
            </div>
        </section>

        {{-- MATERIALEN --}}
        <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
                <h2 class="text-xl font-bold text-[#0F4C81]">
                    Materialen
                </h2>
            </div>

            @if($order->items->count() > 0)
                {{-- MOBIELE WEERGAVE --}}
                <div class="space-y-3 p-4 md:hidden">
                    @foreach($order->items as $item)
                        @php
                            $material = $item->material;
                            $category = $material?->category ?? 'Onbekende categorie';
                            $fallbackImage = $categoryImages[$category] ?? 'sidebar-bg.jpg';

                            $imageUrl = $material?->image
                                ? asset('storage/' . $material->image)
                                : asset('images/' . $fallbackImage);
                        @endphp

                        <article class="flex gap-4 rounded-xl border border-gray-100 bg-gray-50 p-3">
                            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-white p-2">
                                <img
                                    src="{{ $imageUrl }}"
                                    class="max-h-full max-w-full object-contain"
                                    alt="{{ $material?->name ?? 'Materiaal' }}"
                                >
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    {{ $category }}
                                </p>

                                <p class="mt-1 font-bold text-gray-900">
                                    {{ $material?->name ?? 'Onbekend materiaal' }}
                                </p>

                                <p class="mt-2 text-sm font-semibold text-[#0F4C81]">
                                    Aantal: {{ $item->quantity }}
                                </p>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- DESKTOP WEERGAVE --}}
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full min-w-[620px]">
                        <thead>
                        <tr class="border-b bg-gray-50 text-sm text-gray-600">
                            <th class="p-4 text-left">
                                Foto
                            </th>

                            <th class="p-4 text-left">
                                Materiaal
                            </th>

                            <th class="p-4 text-left">
                                Categorie
                            </th>

                            <th class="p-4 text-left">
                                Aantal
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($order->items as $item)
                            @php
                                $material = $item->material;
                                $category = $material?->category ?? 'Onbekende categorie';
                                $fallbackImage = $categoryImages[$category] ?? 'sidebar-bg.jpg';

                                $imageUrl = $material?->image
                                    ? asset('storage/' . $material->image)
                                    : asset('images/' . $fallbackImage);
                            @endphp

                            <tr class="border-b border-gray-100 last:border-0">
                                <td class="p-4">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-lg border border-gray-100 bg-gray-50 p-2">
                                        <img
                                            src="{{ $imageUrl }}"
                                            class="max-h-full max-w-full object-contain"
                                            alt="{{ $material?->name ?? 'Materiaal' }}"
                                        >
                                    </div>
                                </td>

                                <td class="p-4 font-semibold text-gray-900">
                                    {{ $material?->name ?? 'Onbekend materiaal' }}
                                </td>

                                <td class="p-4 text-gray-700">
                                    {{ $category }}
                                </td>

                                <td class="p-4 font-bold text-gray-900">
                                    {{ $item->quantity }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-4 py-10 text-center text-gray-500 italic">
                    Geen materialen gevonden.
                </div>
            @endif
        </section>

        {{-- ACTIES --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
            <a
                href="{{ route('magazijn.orders.edit', $order->id) }}"
                class="inline-flex w-full items-center justify-center sm:w-auto"
            >
                <x-button type="button" class="w-full justify-center sm:w-auto">
                    Bewerk
                </x-button>
            </a>

        </div>
    </div>
</x-app-layout>
