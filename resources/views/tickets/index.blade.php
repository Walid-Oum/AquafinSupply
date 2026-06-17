<x-app-layout>
    <div class="p-4 md:p-8">
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <x-page-header title="Mijn supportaanvragen" />

                <p class="text-gray-600">
                    Bekijk hier de status van je supportaanvragen.
                </p>
            </div>

            <a href="{{ route('tickets.create') }}" class="shrink-0">
                <x-button type="button">
                    Nieuwe supportaanvraag
                </x-button>
            </a>
        </div>

        @if($tickets->count() > 0)
            <div class="mb-6 max-w-md">
                <x-search-bar
                    id="ticket-search-input"
                    placeholder="Zoeken op onderwerp, status of bestelling..."
                    endpoint="{{ route('api.tickets.search') }}"
                />
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div id="tickets-container">
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

                <div
                    class="js-ticket-item mb-4 rounded-xl bg-white p-5 shadow-sm transition hover:bg-gray-50"
                    data-search="{{ $ticketSearchText }}"
                >
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">
                                {{ $ticket->subject }}
                            </h2>

                            <div class="mt-2">
                                <x-status-badge :status="$ticket->status" />
                            </div>

                            <p class="mt-2 text-sm text-gray-600">
                                Bestelling: #{{ $ticket->order_id }}
                            </p>

                            <p class="mt-1 text-sm text-gray-600">
                                Aangemaakt op: {{ $ticket->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>

                    @if($ticket->warehouse_note)
                        <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-gray-700">
                            <p class="font-semibold text-blue-700">
                                Antwoord van magazijn
                            </p>

                            <p class="mt-1">
                                {{ $ticket->warehouse_note }}
                            </p>
                        </div>
                    @else
                        <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm text-gray-500">
                            Nog geen antwoord van het magazijn.
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <p class="text-gray-600 italic">
                        Je hebt nog geen supportaanvraag aangemaakt.
                    </p>
                </div>
            @endforelse

            <div
                id="tickets-empty-state"
                class="hidden rounded-xl bg-white p-6 text-center shadow-sm"
            >
                <p class="text-gray-600 italic">
                    Geen supportaanvragen gevonden voor deze zoekterm.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('ticket-search-input');
            const ticketItems = document.querySelectorAll('.js-ticket-item');
            const emptyState = document.getElementById('tickets-empty-state');

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

            function applyTicketFilter() {
                if (! searchInput) {
                    return;
                }

                const searchValue = searchInput.value;
                let visibleCount = 0;

                ticketItems.forEach(function (item) {
                    const matchesSearch = fuzzyMatches(searchValue, item.dataset.search);

                    if (matchesSearch) {
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

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    applyTicketFilter();
                });
            }

            applyTicketFilter();
        });
    </script>
</x-app-layout>
