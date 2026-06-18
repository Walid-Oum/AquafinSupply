{{--
    MAGAZIJN - BESTELLINGEN OVERZICHT

    @author      
    @version     1.0
    @since       2026-06-18

    Deze view toont een overzicht van alle bestellingen voor magazijnmedewerkers.
    Magazijnmedewerkers kunnen hier de status van bestellingen wijzigen,
    zoeken op bestellingnummer, technieker, status, depot of leverdatum.
    De view bevat zowel een mobile (card) als desktop (tabel) layout.

    @see App\Http\Controllers\Userzone\OrderController::warehouseIndex()
    @see App\Http\Controllers\Userzone\OrderController::warehouseUpdate()
--}}

<x-app-layout>
    <div class="space-y-6">
        <div>
            <x-page-header title="Bestellingen overzicht" />

            <p class="mt-1 text-sm text-gray-600 sm:text-base">
                Bekijk en beheer de bestellingen van techniekers in jouw depot.
            </p>
        </div>

        {{-- FILTERS EN ZOEKBALK --}}
        <section class="rounded-2xl border border-gray-100 bg-white p-3 shadow-sm sm:p-4">
            <div class="grid grid-cols-1 gap-3 lg:grid-cols-3 lg:items-center">
                <div class="lg:col-span-1">
                    <input
                        type="text"
                        id="global-order-search"
                        autocomplete="off"
                        placeholder="Bestelling zoeken..."
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                    >
                </div>

                <form
                    method="GET"
                    action="{{ route('magazijn.orders.index') }}"
                    class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:col-span-2 lg:flex lg:justify-end"
                >
                    <select
                        name="status"
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20 lg:w-56"
                    >
                        <option value="">
                            Alle statussen
                        </option>

                        <option value="Nieuw" @selected(request('status') === 'Nieuw')>
                            Nieuw
                        </option>

                        <option value="In voorbereiding" @selected(request('status') === 'In voorbereiding')>
                            In voorbereiding
                        </option>

                        <option value="Klaar om af te halen" @selected(request('status') === 'Klaar om af te halen')>
                            Klaar om af te halen
                        </option>

                        <option value="Afgehaald" @selected(request('status') === 'Afgehaald')>
                            Afgehaald
                        </option>
                    </select>

                    <x-button type="submit" class="w-full justify-center sm:w-auto">
                        Filter
                    </x-button>

                    <a
                        href="{{ route('magazijn.orders.index') }}"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
                    >
                        Reset
                    </a>
                </form>
            </div>
        </section>

        {{-- BESTELLINGEN LIJST --}}
        <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
            @if($orders->count() > 0)
                {{-- MOBILE CARD LAYOUT --}}
                <div class="space-y-3 p-4 md:hidden">
                    @foreach($orders as $order)
                        @php
                            $orderSearchText = collect([
                                $order->id,
                                'Bestelling #' . $order->id,
                                $order->user?->name,
                                $order->status,
                                $order->location?->province,
                                $order->location?->name,
                                $order->delivery_date,
                            ])->filter()->implode(' ');
                        @endphp

                        <article
                            class="js-order-item order-row rounded-2xl border border-gray-100 bg-gray-50 p-4 shadow-sm"
                            data-search="{{ $orderSearchText }}"
                        >
                            <div class="mb-4 flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Bestelling
                                    </p>

                                    <h2 class="order-id mt-1 text-xl font-bold text-[#0F4C81]">
                                        #{{ $order->id }}
                                    </h2>
                                </div>

                                <div class="order-status shrink-0">
                                    <span class="hidden">{{ $order->status }}</span>
                                    <x-status-badge :status="$order->status" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-3">
                                <div class="rounded-xl bg-white p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Technieker
                                    </p>

                                    <p class="order-technician mt-1 font-semibold text-gray-900">
                                        {{ $order->user?->name ?? 'Onbekend' }}
                                    </p>
                                </div>

                                <div class="rounded-xl bg-white p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Depot/provincie
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-900">
                                        {{ $order->location?->province ?? 'Geen provincie ingesteld' }}
                                    </p>

                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $order->location?->name ?? 'Geen depot gekoppeld' }}
                                    </p>
                                </div>

                                <div class="rounded-xl bg-white p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Leverdatum
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-900">
                                        {{ $order->delivery_date ?? 'Geen leverdatum' }}
                                    </p>
                                </div>
                            </div>

                            {{-- STATUS UPDATE FORM (MOBILE) --}}
                            <div class="mt-4 rounded-xl bg-white p-3">
                                <p class="mb-2 text-sm font-semibold text-gray-700">
                                    Status aanpassen
                                </p>

                                <form
                                    id="order-status-form-mobile-{{ $order->id }}"
                                    action="{{ route('magazijn.orders.update', $order->id) }}"
                                    method="POST"
                                    class="space-y-3"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <select
                                        name="status"
                                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                                    >
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

                                    <x-button type="submit" class="w-full justify-center">
                                        Opslaan
                                    </x-button>
                                </form>
                            </div>

                            <div class="mt-3">
                                <a
                                    href="{{ route('magazijn.orders.show', $order->id) }}"
                                    class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 text-sm font-semibold text-[#0F4C81] transition hover:bg-gray-200"
                                >
                                    Bekijk bestelling
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- DESKTOP TABLE LAYOUT --}}
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full min-w-[980px]">
                        <thead>
                        <tr class="border-b bg-gray-50 text-sm text-gray-600">
                            <th class="p-4 text-left">
                                Nummer
                            </th>

                            <th class="p-4 text-left">
                                Technieker
                            </th>

                            <th class="p-4 text-left">
                                Depot/provincie
                            </th>

                            <th class="p-4 text-left">
                                Leverdatum
                            </th>

                            <th class="p-4 text-left">
                                Status
                            </th>

                            <th class="p-4 text-left">
                                Actie
                            </th>

                            <th class="p-4 text-center">
                                Bekijk
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($orders as $order)
                            @php
                                $orderSearchText = collect([
                                    $order->id,
                                    'Bestelling #' . $order->id,
                                    $order->user?->name,
                                    $order->status,
                                    $order->location?->province,
                                    $order->location?->name,
                                    $order->delivery_date,
                                ])->filter()->implode(' ');
                            @endphp

                            <tr
                                class="js-order-item order-row border-b border-gray-100 transition hover:bg-gray-50 last:border-0"
                                data-search="{{ $orderSearchText }}"
                            >
                                <td class="order-id p-4 font-semibold text-gray-900">
                                    #{{ $order->id }}
                                </td>

                                <td class="order-technician p-4 text-gray-800">
                                    {{ $order->user?->name ?? 'Onbekend' }}
                                </td>

                                <td class="p-4">
                                    <div>
                                        <p class="font-semibold text-gray-900">
                                            {{ $order->location?->province ?? 'Geen provincie ingesteld' }}
                                        </p>

                                        <p class="text-sm text-gray-500">
                                            {{ $order->location?->name ?? 'Geen depot gekoppeld' }}
                                        </p>
                                    </div>
                                </td>

                                <td class="p-4 text-gray-700">
                                    {{ $order->delivery_date ?? 'Geen leverdatum' }}
                                </td>

                                <td class="order-status p-4">
                                    <span class="hidden">{{ $order->status }}</span>
                                    <x-status-badge :status="$order->status" />
                                </td>

                                {{-- STATUS UPDATE FORM (DESKTOP) --}}
                                <td class="p-4">
                                    <form
                                        id="order-status-form-desktop-{{ $order->id }}"
                                        action="{{ route('magazijn.orders.update', $order->id) }}"
                                        method="POST"
                                        class="flex items-center gap-2"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <select
                                            name="status"
                                            class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                                        >
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

                                        <x-button type="submit">
                                            Opslaan
                                        </x-button>
                                    </form>
                                </td>

                                <td class="p-4 text-center">
                                    <a
                                        href="{{ route('magazijn.orders.show', $order->id) }}"
                                        class="font-semibold text-[#0F4C81] hover:underline"
                                    >
                                        Bekijk
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-4 py-10 text-center text-gray-500 italic">
                    Geen bestellingen gevonden.
                </div>
            @endif

            <div
                id="orders-empty-state"
                class="hidden px-4 py-10 text-center text-gray-500 italic"
            >
                Geen bestellingen gevonden voor deze zoekterm.
            </div>
        </section>
    </div>

    {{-- ZOEKFUNCTIE --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            /**
             * Zoek functionaliteit voor bestellingen.
             * Filtert de lijst op basis van bestellingnummer, technieker,
             * status, depot, provincie of leverdatum.
             */
            const searchInput = document.getElementById('global-order-search');
            const orderItems = document.querySelectorAll('.js-order-item');
            const emptyState = document.getElementById('orders-empty-state');

            function normalizeText(value) {
                return (value || '')
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, ' ')
                    .trim();
            }

            function applyOrderSearch() {
                const query = normalizeText(searchInput ? searchInput.value : '');
                let visibleCount = 0;

                orderItems.forEach(function (item) {
                    const searchText = normalizeText(item.dataset.search);

                    if (query === '' || searchText.includes(query)) {
                        item.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        item.classList.add('hidden');
                    }
                });

                if (emptyState) {
                    if (visibleCount === 0 && orderItems.length > 0) {
                        emptyState.classList.remove('hidden');
                    } else {
                        emptyState.classList.add('hidden');
                    }
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', applyOrderSearch);
            }

            applyOrderSearch();
        });
    </script>
</x-app-layout>