<x-app-layout>
    <div class="p-8">
        <div class="mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <x-page-header title="Mijn supportaanvragen" />
                <p class="text-gray-600">
                    Bekijk hier de status van je supportaanvragen.
                </p>
            </div>

            <div class="flex flex-wrap gap-4 items-center w-full md:w-auto justify-end">
                <div class="relative">
                    <input 
                        type="text" 
                        id="ticket-search-input"
                        autocomplete="off"
                        placeholder="Zoek live op onderwerp of status..." 
                        class="border rounded-lg px-3 py-2 w-64 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F4C81] text-black">
                </div>

                <a href="{{ route('tickets.create') }}">
                    <x-button>
                        Nieuwe supportaanvraag
                    </x-button>
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        <div id="tickets-container">
            @forelse ($tickets as $ticket)
                <div class="js-ticket-item mb-4 rounded-lg bg-white p-4 shadow hover:bg-gray-50 transition duration-150">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="js-ticket-subject font-semibold text-gray-900 text-lg">
                                {{ $ticket->subject }}
                            </h2> 

                            <div class="mt-2">
                                <span class="js-ticket-status">
                                    <x-status-badge :status="$ticket->status" />
                                </span>
                            </div>

                            <p class="mt-2 text-sm text-gray-600">
                                Aangemaakt op: {{ $ticket->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>

                    @if($ticket->warehouse_note)
                        <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-gray-700">
                            <p class="font-semibold text-blue-700">
                                Antwoord van magazijn:
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
                <div class="rounded-lg bg-white p-6 shadow">
                    <p class="text-gray-600 italic">
                        Je hebt nog geen supportaanvraag aangemaakt.
                    </p>
                </div>
            @endforelse

            <div id="no-tickets-found" class="hidden rounded-lg bg-white p-6 shadow text-center">
                <p class="text-gray-600 italic">Geen supportaanvragen gevonden die voldoen aan de zoekterm.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('ticket-search-input');
            const ticketItems = document.querySelectorAll('.js-ticket-item');
            const noResultsMessage = document.getElementById('no-tickets-found');

            function normalizeText(text) {
                if (!text) return '';
                return text.toLowerCase()
                    .normalize("NFD").replace(/[\u0300-\u036f]/g, "") // Accenten weg (é -> e)
                    .replace(/[^a-z0-9]/g, ' ')                      // Speciale tekens weg
                    .replace(/\s+/g, ' ')                             // Dubbele spaties weg
                    .trim();
            }

            function levenshtein(a, b) {
                const matrix = [];
                for (let i = 0; i <= b.length; i++) matrix[i] = [i];
                for (let j = 0; j <= a.length; j++) matrix[0][j] = j;

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

            function getAllowedDistance(word) {
                const length = word.length;
                if (length <= 4) return 1;
                if (length <= 7) return 2;
                if (length <= 12) return 3;
                return 4;
            }

            searchInput.addEventListener('input', function () {
                const queryClean = normalizeText(this.value);
                const flatQuery = queryClean.replace(/ /g, '');
                let visibleCount = 0;

                if (queryClean === '') {
                    ticketItems.forEach(item => item.style.display = '');
                    noResultsMessage.classList.add('hidden');
                    return;
                }

                ticketItems.forEach(function (item) {
                    const subjectEl = item.querySelector('.js-ticket-subject');
                    const statusEl = item.querySelector('.js-ticket-status');
                    
                    const subject = normalizeText(subjectEl ? subjectEl.textContent : '');
                    const status = normalizeText(statusEl ? statusEl.textContent : '');
                    
                    const flatSubject = subject.replace(/ /g, '');
                    const flatStatus = status.replace(/ /g, '');

                    let matches = false;

                    // 1. Directe match of spatieloze match
                    if (subject.includes(queryClean) || status.includes(queryClean) || 
                        flatSubject.includes(flatQuery) || flatStatus.includes(flatQuery)) {
                        matches = true;
                    } 
                    // 2. Fuzzy match met Levenshtein (voor typfouten)
                    else if (flatQuery.length >= 3) {
                        const allowedDistance = getAllowedDistance(flatQuery);
                        if (levenshtein(flatQuery, flatSubject) <= allowedDistance || 
                            levenshtein(flatQuery, flatStatus) <= allowedDistance) {
                            matches = true;
                        }
                    }

                    if (matches) {
                        item.style.display = '';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Toon een melding als de filterlijst helemaal leeg is
                if (visibleCount === 0 && ticketItems.length > 0) {
                    noResultsMessage.classList.remove('hidden');
                } else {
                    noResultsMessage.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>