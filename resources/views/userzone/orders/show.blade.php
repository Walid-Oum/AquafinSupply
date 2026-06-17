{{--
    Pagina: Detail bestelling

    User Stories:
    US17 - Inhoud bestelling bekijken
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
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <x-page-header title="Bestelling #{{ $order->id }}" />

                <p class="mt-1 text-sm text-gray-600 sm:text-base">
                    Bekijk hier de gegevens en materialen van je bestelling.
                </p>
            </div>

            <a
                href="{{ route('orders.index') }}"
                class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
            >
                ← Terug naar bestellingen
            </a>
        </div>

        {{-- Bestelgegevens --}}
        <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-[#0F4C81]">
                        Bestelgegevens
                    </h2>

                    <p class="text-sm text-gray-500">
                        Algemene informatie over deze bestelling.
                    </p>
                </div>

                <x-status-badge :status="$order->status" />
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-xl bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Bestelling ID
                    </p>

                    <p class="mt-1 text-lg font-bold text-gray-900">
                        #{{ $order->id }}
                    </p>
                </div>

                <div class="rounded-xl bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Besteld op
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        {{ $order->created_at?->format('d/m/Y') ?? 'Onbekend' }}
                    </p>
                </div>

                <div class="rounded-xl bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Leverdatum
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        {{ $order->delivery_date ?? 'Geen leverdatum' }}
                    </p>
                </div>

                <div class="rounded-xl bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Technieker
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        {{ $order->user?->name ?? 'Onbekend' }}
                    </p>
                </div>

                <div class="rounded-xl bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Depot/provincie
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        {{ $order->location?->province ?? 'Geen locatie gekoppeld' }}
                    </p>
                </div>

                <div class="rounded-xl bg-gray-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Depot
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        {{ $order->location?->name ?? 'Geen depot gekoppeld' }}
                    </p>
                </div>

                @if($order->location?->depot_address)
                    <div class="rounded-xl bg-gray-50 p-4 sm:col-span-2 xl:col-span-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Depotadres
                        </p>

                        <p class="mt-1 font-semibold text-gray-900">
                            {{ $order->location->depot_address }}
                        </p>
                    </div>
                @endif

                <div class="rounded-xl bg-gray-50 p-4 sm:col-span-2 xl:col-span-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Opmerking
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        {{ $order->comment ?? 'Geen opmerking' }}
                    </p>
                </div>
            </div>
        </section>

        {{-- Bestelde materialen --}}
        <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-4 py-4 sm:px-5">
                <h2 class="text-lg font-bold text-[#0F4C81]">
                    Bestelde materialen
                </h2>

                <p class="text-sm text-gray-500">
                    Overzicht van alle materialen in deze bestelling.
                </p>
            </div>

            @if($order->items->count() > 0)
                {{-- Mobile card layout --}}
                <div class="space-y-3 p-4 md:hidden">
                    @foreach($order->items as $item)
                        @php
                            $material = $item->material;
                            $category = $material?->category ?? 'Onbekende categorie';
                            $fallbackImage = $categoryImages[$category] ?? 'sidebar-bg.jpg';

                            $imageUrl = $material?->image
                                ? \Illuminate\Support\Facades\Storage::url($material->image)
                                : asset('images/' . $fallbackImage);
                        @endphp

                        <article class="rounded-2xl border border-gray-100 bg-gray-50 p-4 shadow-sm">
                            <div class="flex gap-4">
                                <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-xl bg-white p-2">
                                    <img
                                        src="{{ $imageUrl }}"
                                        class="max-h-full max-w-full object-contain"
                                        alt="{{ $material?->name ?? 'Materiaal' }}"
                                    >
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h3 class="font-bold leading-snug text-gray-900">
                                        {{ $material?->name ?? 'Onbekend materiaal' }}
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $category }}
                                    </p>

                                    <div class="mt-3 inline-flex rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700">
                                        Hoeveelheid: {{ $item->quantity }}
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Desktop table layout --}}
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full min-w-[620px]">
                        <thead>
                        <tr class="border-b bg-gray-50 text-sm text-gray-600">
                            <th class="p-4 text-left">
                                Afbeelding
                            </th>

                            <th class="p-4 text-left">
                                Materiaal
                            </th>

                            <th class="p-4 text-left">
                                Categorie
                            </th>

                            <th class="p-4 text-left">
                                Hoeveelheid
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
                                    ? \Illuminate\Support\Facades\Storage::url($material->image)
                                    : asset('images/' . $fallbackImage);
                            @endphp

                            <tr class="border-b border-gray-100 transition hover:bg-gray-50 last:border-0">
                                <td class="p-4">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-xl border border-gray-100 bg-gray-50 p-2">
                                        <img
                                            src="{{ $imageUrl }}"
                                            class="max-h-full max-w-full object-contain"
                                            alt="{{ $material?->name ?? 'Materiaal' }}"
                                        >
                                    </div>
                                </td>

                                <td class="p-4 font-medium text-gray-900">
                                    {{ $material?->name ?? 'Onbekend materiaal' }}
                                </td>

                                <td class="p-4 text-gray-700">
                                    {{ $category }}
                                </td>

                                <td class="p-4 font-semibold text-gray-900">
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
    </div>
</x-app-layout>
