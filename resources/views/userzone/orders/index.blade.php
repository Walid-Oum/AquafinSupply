{{--
    Pagina: Overzicht bestellingen

    User Stories:
    US12 - Eigen bestellingen bekijken
--}}

<x-app-layout>
    <div class="p-4 md:p-8">
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <x-page-header title="Mijn bestellingen" />

                <p class="text-gray-600">
                    Bekijk hier al je geplaatste bestellingen en hun huidige status.
                </p>
            </div>
        </div>

        @if($orders->count() > 0)
            <div class="mb-6 flex flex-col gap-4">
                <div class="max-w-md">
                    <x-search-bar
                        id="order-search-input"
                        placeholder="Zoeken op ID, status of leverdatum..."
                    />
                </div>

                <div class="flex flex-wrap gap-3">
                    <button
                        type="button"
                        data-status-filter="all"
                        class="js-order-status-filter rounded-full bg-[#0F4C81] px-5 py-2 text-xs font-medium text-white shadow-sm transition"
                    >
                        Alles
                    </button>

                    <button
                        type="button"
                        data-status-filter="Nieuw"
                        class="js-order-status-filter rounded-full bg-gray-100 px-5 py-2 text-xs font-medium text-gray-600 transition hover:bg-gray-200"
                    >
                        Nieuw
                    </button>

                    <button
                        type="button"
                        data-status-filter="In voorbereiding"
                        class="js-order-status-filter rounded-full bg-gray-100 px-5 py-2 text-xs font-medium text-gray-600 transition hover:bg-gray-200"
                    >
                        In voorbereiding
                    </button>

                    <button
                        type="button"
                        data-status-filter="Klaar om af te halen"
                        class="js-order-status-filter rounded-full bg-gray-100 px-5 py-2 text-xs font-medium text-gray-600 transition hover:bg-gray-200"
                    >
                        Klaar om af te halen
                    </button>

                    <button
                        type="button"
                        data-status-filter="archive"
                        class="js-order-status-filter rounded-full bg-gray-100 px-5 py-2 text-xs font-medium text-gray-600 transition hover:bg-gray-200"
                    >
                        Archief
                    </button>
                </div>
            </div>
        @endif

        <x-card>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[680px]">
                    <thead>
                    <tr class="border-b text-sm text-gray-600">
                        <th class="p-3 text-left">
                            ID
                        </th>

                        <th class="p-3 text-left">
                            Besteld op
                        </th>

                        <th class="p-3 text-left">
                            Leverdatum
                        </th>

                        <th class="p-3 text-left">
                            Status
                        </th>

                        <th class="p-3 text-left">
                            Actie
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse($orders as $order)
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
                            <td class="p-3 font-medium text-gray-900">
                                #{{ $order->id }}
                            </td>

                            <td class="p-3 text-gray-700">
                                {{ $order->created_at->format('d/m/Y') }}
                            </td>

                            <td class="p-3 text-gray-700">
                                {{ $order->delivery_date ?? 'Geen leverdatum' }}
                            </td>

                            <td class="p-3">
                                <x-status-badge :status="$order->status" />
                            </td>

                            <td class="p-3">
                                <a href="{{ route('orders.show', $order->id) }}">
                                    <x-button type="button">
                                        Bekijken
                                    </x-button>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500 italic">
                                Geen bestellingen gevonden.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div
                id="orders-empty-state"
                class="hidden p-6 text-center text-gray-500 italic"
            >
                Geen bestellingen gevonden voor deze zoekterm of filter.
            </div>
        </x-card>
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
                    button.classList.remove('bg-[#0F4C81]', 'text-white', 'shadow-sm');
                    button.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                });

                activeButton.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                activeButton.classList.add('bg-[#0F4C81]', 'text-white', 'shadow-sm');
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
