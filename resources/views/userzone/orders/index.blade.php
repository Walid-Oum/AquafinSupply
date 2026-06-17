{{--
    Pagina: Overzicht bestellingen

    User Stories:
    US12 - Eigen bestellingen bekijken
--}}

<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <x-page-header title="Mijn bestellingen" />

                <p class="mt-1 text-sm text-gray-600 sm:text-base">
                    Bekijk hier al je geplaatste bestellingen en hun huidige status.
                </p>
            </div>
        </div>

        @if($orders->count() > 0)
            <section class="rounded-2xl border border-gray-100 bg-white p-3 shadow-sm sm:p-4">
                <div class="grid grid-cols-1 gap-3 lg:grid-cols-3 lg:items-center">
                    <div class="lg:col-span-1">
                        <x-search-bar
                            id="order-search-input"
                            placeholder="Zoeken op ID, status of leverdatum..."
                        />
                    </div>

                    <div class="-mx-3 overflow-x-auto px-3 pb-1 lg:col-span-2 lg:mx-0 lg:px-0">
                        <div class="flex min-w-max gap-2 lg:min-w-0 lg:flex-wrap lg:justify-end">
                            <button
                                type="button"
                                data-status-filter="all"
                                class="js-order-status-filter whitespace-nowrap rounded-full border border-[#0F4C81] bg-[#0F4C81] px-4 py-2 text-sm font-semibold text-white shadow-sm transition"
                            >
                                Alles
                            </button>

                            <button
                                type="button"
                                data-status-filter="Nieuw"
                                class="js-order-status-filter whitespace-nowrap rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                            >
                                Nieuw
                            </button>

                            <button
                                type="button"
                                data-status-filter="In voorbereiding"
                                class="js-order-status-filter whitespace-nowrap rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                            >
                                In voorbereiding
                            </button>

                            <button
                                type="button"
                                data-status-filter="Klaar om af te halen"
                                class="js-order-status-filter whitespace-nowrap rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                            >
                                Klaar om af te halen
                            </button>

                            <button
                                type="button"
                                data-status-filter="archive"
                                class="js-order-status-filter whitespace-nowrap rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                            >
                                Archief
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
            @if($orders->count() > 0)
                {{-- Mobile card layout --}}
                <div class="space-y-3 p-4 md:hidden">
                    @foreach($orders as $order)
                        @php
                            $orderSearchText = collect([
                                $order->id,
                                'Bestelling #' . $order->id,
                                $order->status,
                                $order->created_at?->format('d/m/Y'),
                                $order->delivery_date,
                            ])->filter()->implode(' ');
                        @endphp

                        <article
                            class="js-order-item rounded-2xl border border-gray-100 bg-gray-50 p-4 shadow-sm"
                            data-search="{{ $orderSearchText }}"
                            data-status="{{ $order->status }}"
                        >
                            <div class="mb-4 flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Bestelling
                                    </p>

                                    <h2 class="mt-1 text-xl font-bold text-[#0F4C81]">
                                        #{{ $order->id }}
                                    </h2>
                                </div>

                                <x-status-badge :status="$order->status" />
                            </div>

                            <div class="grid grid-cols-1 gap-3">
                                <div class="rounded-xl bg-white p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Besteld op
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800">
                                        {{ $order->created_at->format('d/m/Y') }}
                                    </p>
                                </div>

                                <div class="rounded-xl bg-white p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Leverdatum
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-800">
                                        {{ $order->delivery_date ?? 'Geen leverdatum' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a
                                    href="{{ route('orders.show', $order->id) }}"
                                    class="block"
                                >
                                    <x-button type="button" class="w-full justify-center">
                                        Bekijken
                                    </x-button>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Desktop table layout --}}
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full min-w-[680px]">
                        <thead>
                        <tr class="border-b bg-gray-50 text-sm text-gray-600">
                            <th class="p-4 text-left">
                                ID
                            </th>

                            <th class="p-4 text-left">
                                Besteld op
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
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($orders as $order)
                            @php
                                $orderSearchText = collect([
                                    $order->id,
                                    'Bestelling #' . $order->id,
                                    $order->status,
                                    $order->created_at?->format('d/m/Y'),
                                    $order->delivery_date,
                                ])->filter()->implode(' ');
                            @endphp

                            <tr
                                class="js-order-item border-b border-gray-100 transition hover:bg-gray-50 last:border-0"
                                data-search="{{ $orderSearchText }}"
                                data-status="{{ $order->status }}"
                            >
                                <td class="p-4 font-medium text-gray-900">
                                    #{{ $order->id }}
                                </td>

                                <td class="p-4 text-gray-700">
                                    {{ $order->created_at->format('d/m/Y') }}
                                </td>

                                <td class="p-4 text-gray-700">
                                    {{ $order->delivery_date ?? 'Geen leverdatum' }}
                                </td>

                                <td class="p-4">
                                    <x-status-badge :status="$order->status" />
                                </td>

                                <td class="p-4">
                                    <a href="{{ route('orders.show', $order->id) }}">
                                        <x-button type="button">
                                            Bekijken
                                        </x-button>
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
                Geen bestellingen gevonden voor deze zoekterm of filter.
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('order-search-input');
            const orderItems = document.querySelectorAll('.js-order-item');
            const emptyState = document.getElementById('orders-empty-state');
            const statusButtons = document.querySelectorAll('.js-order-status-filter');

            let selectedStatus = 'all';

            function normalizeText(value) {
                return (value || '')
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, ' ')
                    .trim();
            }

            function levenshtein(a, b) {
                const matrix = [];

                for (let i = 0; i <= b.length; i++) {
                    matrix[i] = [i];
                }

                for (let j = 0; j <= a.length; j++) {
                    matrix[0][j] = j;
                }

                for (let i = 1; i <= b.length; i++) {
                    for (let j = 1; j <= a.length; j++) {
                        if (b.charAt(i - 1) === a.charAt(j - 1)) {
                            matrix[i][j] = matrix[i - 1][j - 1];
                        } else {
                            matrix[i][j] = Math.min(
                                matrix[i - 1][j - 1] + 1,
                                matrix[i][j - 1] + 1,
                                matrix[i - 1][j] + 1
                            );
                        }
                    }
                }

                return matrix[b.length][a.length];
            }

            function allowedDistance(word) {
                if (word.length <= 5) {
                    return 1;
                }

                if (word.length <= 9) {
                    return 2;
                }

                return 3;
            }

            function wordMatches(queryWord, textWords) {
                if (queryWord.length === 0) {
                    return true;
                }

                for (const textWord of textWords) {
                    if (textWord.includes(queryWord)) {
                        return true;
                    }

                    if (queryWord.length < 4) {
                        continue;
                    }

                    if (levenshtein(queryWord, textWord) <= allowedDistance(queryWord)) {
                        return true;
                    }
                }

                return false;
            }

            function fuzzyMatches(query, text) {
                const normalizedQuery = normalizeText(query);
                const normalizedText = normalizeText(text);

                if (normalizedQuery === '') {
                    return true;
                }

                if (normalizedText.includes(normalizedQuery)) {
                    return true;
                }

                const queryWords = normalizedQuery.split(' ').filter(Boolean);
                const textWords = normalizedText.split(' ').filter(Boolean);

                return queryWords.every(function (queryWord) {
                    return wordMatches(queryWord, textWords);
                });
            }

            function statusMatches(orderStatus) {
                if (selectedStatus === 'all') {
                    return true;
                }

                if (selectedStatus === 'archive') {
                    return orderStatus === 'Afgehaald' || orderStatus === 'Geannuleerd';
                }

                return orderStatus === selectedStatus;
            }

            function updateStatusButtons(activeButton) {
                statusButtons.forEach(function (button) {
                    button.classList.remove('border-[#0F4C81]', 'bg-[#0F4C81]', 'text-white', 'shadow-sm');
                    button.classList.add('border-gray-200', 'bg-white', 'text-gray-700', 'hover:bg-gray-50');
                });

                activeButton.classList.remove('border-gray-200', 'bg-white', 'text-gray-700', 'hover:bg-gray-50');
                activeButton.classList.add('border-[#0F4C81]', 'bg-[#0F4C81]', 'text-white', 'shadow-sm');
            }

            function applyOrderFilter() {
                const searchValue = searchInput ? searchInput.value : '';
                let visibleCount = 0;

                orderItems.forEach(function (item) {
                    const matchesSearch = fuzzyMatches(searchValue, item.dataset.search);
                    const matchesStatus = statusMatches(item.dataset.status);

                    if (matchesSearch && matchesStatus) {
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

            statusButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    selectedStatus = button.dataset.statusFilter;
                    updateStatusButtons(button);
                    applyOrderFilter();
                });
            });

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    applyOrderFilter();
                });
            }

            applyOrderFilter();
        });
    </script>
</x-app-layout>
