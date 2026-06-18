{{--
    Pagina: Bestellingen overzicht

    Doel:
    Geeft administrators een overzicht van alle
    geplaatste bestellingen binnen het systeem.

    Functionaliteiten:
    - Overzicht van alle bestellingen
    - Filteren op status
    - Filteren op depot
    - Zoeken op bestelnummer, technieker of status
    - Bekijken van bestelgegevens
    - Responsieve weergave voor desktop en mobiel

    Gebruikersrol:
    - Admin
--}}

<x-app-layout>
    <div class="min-w-0 max-w-full space-y-6 overflow-x-hidden">
        {{-- HEADER --}}
        <div>
            <x-page-header title="Alle bestellingen" />

            <p class="mt-1 text-sm text-gray-600 sm:text-base">
                Bekijk en filter alle geplaatste bestellingen binnen het systeem.
            </p>
        </div>

        {{-- ZOEKEN EN FILTEREN --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="w-full lg:max-w-sm">
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
                    action="{{ route('admin.orders.index') }}"
                    class="flex w-full flex-col gap-3 sm:flex-row lg:w-auto"
                >
                    <select
                        name="status"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20 sm:w-auto"
                    >
                        <option value="all">Alle statussen</option>

                        <option value="Nieuw" {{ request('status') == 'Nieuw' ? 'selected' : '' }}>
                            Nieuw
                        </option>

                        <option value="In voorbereiding" {{ request('status') == 'In voorbereiding' ? 'selected' : '' }}>
                            In voorbereiding
                        </option>

                        <option value="Klaar om af te halen" {{ request('status') == 'Klaar om af te halen' ? 'selected' : '' }}>
                            Klaar om af te halen
                        </option>

                        <option value="Afgehaald" {{ request('status') == 'Afgehaald' ? 'selected' : '' }}>
                            Afgehaald
                        </option>

                        <option value="Geannuleerd" {{ request('status') == 'Geannuleerd' ? 'selected' : '' }}>
                            Geannuleerd
                        </option>
                    </select>

                    <select
                        name="location_id"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20 sm:w-auto"
                    >
                        <option value="">
                            Alle depots
                        </option>

                        @foreach($locations as $location)
                            <option
                                value="{{ $location->id }}"
                                {{ request('location_id') == $location->id ? 'selected' : '' }}
                            >
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>

                    <x-button class="w-full justify-center sm:w-auto">
                        Filter
                    </x-button>

                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
                    >
                        Reset
                    </a>
                </form>
            </div>
        </div>

        {{-- DESKTOPWEERGAVE --}}
        <div class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm lg:block">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] divide-y divide-gray-200 text-left">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            ID
                        </th>

                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Technieker
                        </th>

                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Besteld op
                        </th>

                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Leverdatum
                        </th>

                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Status
                        </th>

                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Actie
                        </th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($orders as $order)
                        <tr class="order-row transition-colors hover:bg-gray-50/70">
                            <td class="order-id whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900">
                                #{{ $order->id }}
                            </td>

                            <td class="order-technician whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                {{ $order->user->name }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $order->created_at->format('d/m/Y') }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $order->delivery_date }}
                            </td>

                            <td class="order-status whitespace-nowrap px-6 py-4 text-sm">
                                <span class="hidden">{{ $order->status }}</span>
                                <x-status-badge :status="$order->status" />
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <a href="{{ route('admin.orders.show', $order->id) }}">
                                    <x-button>
                                        Bekijken
                                    </x-button>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                Geen bestellingen gevonden.
                            </td>
                        </tr>
                    @endforelse

                    <tr id="no-orders-found-row" class="hidden">
                        <td colspan="6" class="bg-gray-50 px-6 py-8 text-center italic text-gray-500">
                            Geen bestellingen gevonden die voldoen aan de zoekterm.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MOBIELE WEERGAVE --}}
        <div class="space-y-4 lg:hidden">
            @forelse($orders as $order)
                <article class="order-card rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                    <div class="flex flex-col gap-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="order-id-mobile text-lg font-bold text-gray-900">
                                    #{{ $order->id }}
                                </p>

                                <p class="order-technician-mobile text-sm text-gray-500">
                                    {{ $order->user->name }}
                                </p>
                            </div>

                            <div class="order-status-mobile shrink-0">
                                <span class="hidden">{{ $order->status }}</span>
                                <x-status-badge :status="$order->status" />
                            </div>
                        </div>

                        <div class="grid gap-2 text-sm text-gray-700">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-gray-500">Besteld op</span>
                                <span class="font-medium">{{ $order->created_at->format('d/m/Y') }}</span>
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <span class="text-gray-500">Leverdatum</span>
                                <span class="font-medium">{{ $order->delivery_date }}</span>
                            </div>
                        </div>

                        <a href="{{ route('admin.orders.show', $order->id) }}">
                            <x-button class="w-full justify-center">
                                Bekijken
                            </x-button>
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-gray-100 bg-white p-6 text-center text-gray-500 shadow-sm">
                    Geen bestellingen gevonden.
                </div>
            @endforelse

            <div
                id="no-orders-found-mobile"
                class="hidden rounded-2xl border border-gray-100 bg-white p-6 text-center text-gray-500 shadow-sm"
            >
                Geen bestellingen gevonden die voldoen aan de zoekterm.
            </div>
        </div>
    </div>

    {{-- CLIENT-SIDE ZOEKFUNCTIE --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('global-order-search');
            const rows = document.querySelectorAll('.order-row');
            const cards = document.querySelectorAll('.order-card');
            const noResultsRow = document.getElementById('no-orders-found-row');
            const noResultsMobile = document.getElementById('no-orders-found-mobile');

            if (!searchInput) return;

            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();

                let visibleRows = 0;
                let visibleCards = 0;

                if (query === '') {
                    rows.forEach(row => row.style.display = '');
                    cards.forEach(card => card.classList.remove('hidden'));
                    noResultsRow?.classList.add('hidden');
                    noResultsMobile?.classList.add('hidden');
                    return;
                }

                rows.forEach(row => {
                    const id = row.querySelector('.order-id')?.textContent.toLowerCase() || '';
                    const technician = row.querySelector('.order-technician')?.textContent.toLowerCase() || '';
                    const status = row.querySelector('.order-status')?.textContent.toLowerCase() || '';

                    if (id.includes(query) || technician.includes(query) || status.includes(query)) {
                        row.style.display = '';
                        visibleRows++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                cards.forEach(card => {
                    const id = card.querySelector('.order-id-mobile')?.textContent.toLowerCase() || '';
                    const technician = card.querySelector('.order-technician-mobile')?.textContent.toLowerCase() || '';
                    const status = card.querySelector('.order-status-mobile')?.textContent.toLowerCase() || '';

                    if (id.includes(query) || technician.includes(query) || status.includes(query)) {
                        card.classList.remove('hidden');
                        visibleCards++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                if (visibleRows === 0) {
                    noResultsRow?.classList.remove('hidden');
                } else {
                    noResultsRow?.classList.add('hidden');
                }

                if (visibleCards === 0) {
                    noResultsMobile?.classList.remove('hidden');
                } else {
                    noResultsMobile?.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
