{{--
    TECHNIEKER - MIJN SUPPORTAANVRAGEN (TICKETS OVERZICHT)

    @author      
    @version     1.0
    @since       2026-06-18

    Deze view toont alle supportaanvragen van de ingelogde technieker.
    Techniekers kunnen hun tickets bekijken, filteren op status,
    zoeken op onderwerp, status of bestelling.
    De view bevat ook een knop om een nieuwe supportaanvraag aan te maken.
    Het magazijnantwoord wordt getoond indien beschikbaar.

    @see App\Http\Controllers\TicketController::index()
--}}

<x-app-layout>
    <div class="space-y-6">
        {{-- HEADER --}}
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <x-page-header title="Mijn supportaanvragen" />

                <p class="mt-1 text-sm text-gray-600 sm:text-base">
                    Bekijk hier de status van je supportaanvragen.
                </p>
            </div>

            <a href="{{ route('tickets.create') }}" class="w-full shrink-0 sm:w-auto">
                <x-button type="button" class="w-full justify-center sm:w-auto">
                    Nieuwe supportaanvraag
                </x-button>
            </a>
        </div>

        {{-- FILTERS EN ZOEKBALK --}}
        @if($tickets->count() > 0)
            <section class="rounded-2xl border border-gray-100 bg-white p-3 shadow-sm sm:p-4">
                <div class="grid grid-cols-1 gap-3 lg:grid-cols-3 lg:items-center">
                    <div class="lg:col-span-1">
                        <x-search-bar
                            id="ticket-search-input"
                            placeholder="Zoeken op onderwerp, status of bestelling..."
                            endpoint="{{ route('api.tickets.search') }}"
                        />
                    </div>

                    <div class="-mx-3 overflow-x-auto px-3 pb-1 lg:col-span-2 lg:mx-0 lg:px-0">
                        <div class="flex min-w-max gap-2 lg:min-w-0 lg:flex-wrap lg:justify-end">
                            {{-- STATUS FILTERS --}}
                            <button
                                type="button"
                                data-status-filter="all"
                                class="js-ticket-status-filter whitespace-nowrap rounded-full border border-[#0F4C81] bg-[#0F4C81] px-4 py-2 text-sm font-semibold text-white shadow-sm transition"
                            >
                                Alles
                            </button>

                            <button
                                type="button"
                                data-status-filter="Open"
                                class="js-ticket-status-filter whitespace-nowrap rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                            >
                                Open
                            </button>

                            <button
                                type="button"
                                data-status-filter="In behandeling"
                                class="js-ticket-status-filter whitespace-nowrap rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                            >
                                In behandeling
                            </button>

                            <button
                                type="button"
                                data-status-filter="Opgelost"
                                class="js-ticket-status-filter whitespace-nowrap rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                            >
                                Opgelost
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- TICKETS LIJST --}}
        <section id="tickets-container" class="space-y-4">
            @forelse ($tickets as $ticket)
                @php
                    $ticketSearchText = collect([
                        $ticket->subject,
                        $ticket->description,
                        $ticket->warehouse_note,
                        $ticket->status,
                        $ticket->order_id,
                        'Bestelling #' . $ticket->order_id,
                        $ticket->created_at->format('d/m/Y'),
                    ])->filter()->implode(' ');
                @endphp

                <article
                    class="js-ticket-item rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition hover:shadow-md sm:p-5"
                    data-search="{{ $ticketSearchText }}"
                    data-status="{{ $ticket->status }}"
                >
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Supportaanvraag
                                    </p>

                                    <h2 class="mt-1 text-lg font-bold leading-snug text-[#0F4C81] sm:text-xl">
                                        {{ $ticket->subject }}
                                    </h2>
                                </div>

                                <div class="shrink-0">
                                    <x-status-badge :status="$ticket->status" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div class="rounded-xl bg-gray-50 p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Bestelling
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-900">
                                        #{{ $ticket->order_id }}
                                    </p>
                                </div>

                                <div class="rounded-xl bg-gray-50 p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Aangemaakt op
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-900">
                                        {{ $ticket->created_at->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>

                            {{-- BESCHRIJVING --}}
                            @if($ticket->description)
                                <div class="mt-4 rounded-xl border border-gray-100 bg-gray-50 p-4 text-sm text-gray-700">
                                    <p class="font-semibold text-gray-800">
                                        Beschrijving
                                    </p>

                                    <p class="mt-1 leading-relaxed">
                                        {{ $ticket->description }}
                                    </p>
                                </div>
                            @endif

                            {{-- MAGAZIJN ANTWOORD --}}
                            @if($ticket->warehouse_note)
                                <div class="mt-4 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-gray-700">
                                    <p class="font-semibold text-blue-700">
                                        Antwoord van magazijn
                                    </p>

                                    <p class="mt-1 leading-relaxed">
                                        {{ $ticket->warehouse_note }}
                                    </p>
                                </div>
                            @else
                                <div class="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-500">
                                    Nog geen antwoord van het magazijn.
                                </div>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-gray-100 bg-white p-6 text-center shadow-sm">
                    <p class="text-gray-600 italic">
                        Je hebt nog geen supportaanvraag aangemaakt.
                    </p>

                    <a href="{{ route('tickets.create') }}" class="mt-4 inline-flex">
                        <x-button type="button">
                            Eerste supportaanvraag maken
                        </x-button>
                    </a>
                </div>
            @endforelse

            <div
                id="tickets-empty-state"
                class="hidden rounded-2xl border border-gray-100 bg-white p-6 text-center shadow-sm"
            >
                <p class="text-gray-600 italic">
                    Geen supportaanvragen gevonden voor deze zoekterm of filter.
                </p>
            </div>
        </section>
    </div>

    {{-- ZOEK- EN FILTERFUNCTIE --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('ticket-search-input');
            const ticketItems = document.querySelectorAll('.js-ticket-item');
            const emptyState = document.getElementById('tickets-empty-state');
            const statusButtons = document.querySelectorAll('.js-ticket-status-filter');

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

            function statusMatches(ticketStatus) {
                if (selectedStatus === 'all') {
                    return true;
                }

                return ticketStatus === selectedStatus;
            }

            function updateStatusButtons(activeButton) {
                statusButtons.forEach(function (button) {
                    button.classList.remove('border-[#0F4C81]', 'bg-[#0F4C81]', 'text-white', 'shadow-sm');
                    button.classList.add('border-gray-200', 'bg-white', 'text-gray-700', 'hover:bg-gray-50');
                });

                activeButton.classList.remove('border-gray-200', 'bg-white', 'text-gray-700', 'hover:bg-gray-50');
                activeButton.classList.add('border-[#0F4C81]', 'bg-[#0F4C81]', 'text-white', 'shadow-sm');
            }

            function applyTicketFilter() {
                const searchValue = searchInput ? searchInput.value : '';
                let visibleCount = 0;

                ticketItems.forEach(function (item) {
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
                    if (visibleCount === 0 && ticketItems.length > 0) {
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
                    applyTicketFilter();
                });
            });

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    applyTicketFilter();
                });
            }

            applyTicketFilter();
        });
    </script>
</x-app-layout>