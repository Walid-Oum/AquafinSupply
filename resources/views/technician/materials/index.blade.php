<x-app-layout>
    <x-page-header title="Materialen overzicht" />

    @if($recommendedMaterials->count() > 0)
        <div class="mb-4">

    <span class="font-semibold">
        Huidig overstromingsrisico:
    </span>

            <span
                class="
        inline-block px-3 py-1 rounded-full text-xs font-semibold

        @if($riskLevel === 'Hoog')
            bg-red-100 text-red-700
        @elseif($riskLevel === 'Gemiddeld')
            bg-yellow-100 text-yellow-700
        @else
            bg-green-100 text-green-700
        @endif
        "
            >
        {{ $riskLevel }}
    </span>
        </div>
        <p class="mb-2 text-sm font-semibold text-[#0F4C81]">
            {{ $recommendedMaterials->count() }} aanbevolen materialen gevonden
        </p>
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-bold text-green-700">
                    Aanbevolen materialen (op basis van overstromingsrisico)
                </h2>

                <button
                    type="button"
                    onclick="toggleRecommendations()"
                    id="recommendationIcon"
                    class="text-sm font-medium text-green-700 hover:text-green-900 hover:underline"
                >
                    ▲ Verberg
                </button>
            </div>

            <div id="recommendationsContainer">
                <div class="relative">
    //hoofdcontainer 
    <div class="min-w-0 max-w-full space-y-6 overflow-x-hidden">
        <div>
            <x-page-header title="Materialen overzicht" />

            <p class="mt-1 text-sm text-gray-600 sm:text-base">
                Bekijk materialen, zoek op categorie en voeg materiaal toe aan je winkelmandje.
            </p>
        </div>
//aanbevolen materialen sectie
        @if($recommendedMaterials->count() > 0)
            <section class="w-full max-w-full overflow-hidden rounded-2xl border border-green-200 bg-green-50 shadow-sm">
                <div class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-start sm:justify-between sm:px-5">
                    <div class="min-w-0">
                        <h2 class="text-lg font-bold text-green-700">
                            Aanbevolen materialen
                        </h2>

                        <p class="mt-1 text-sm text-green-700">
                            {{ $recommendedMaterials->count() }} materialen aanbevolen op basis van het overstromingsrisico.
                        </p>
                    </div>
// toon/verberg aanbevelingen knop
                    <button
                        type="button"
                        onclick="toggleRecommendations()"
                        id="recommendationIcon"
                        class="inline-flex w-fit shrink-0 items-center justify-center rounded-full bg-white px-4 py-2 text-sm font-semibold text-green-700 shadow-sm transition hover:bg-green-100"
                    >
                        ▲ Verberg
                    </button>
                </div>
// aanbevolen materialen container
                <div
                    id="recommendationsContainer"
                    class="w-full max-w-full overflow-hidden pb-4"
                >
                    <div class="w-full max-w-full overflow-x-auto overscroll-x-contain px-4 pb-3 sm:px-5">
                        <div class="flex w-max gap-4">
                            @foreach($recommendedMaterials as $material)
                                <div class="w-56 shrink-0">
                                    <x-material-card
                                        :material="$material"
                                        :compact="true"
                                    />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif
// sorteer en zoek sectie
        <section class="rounded-2xl border border-gray-100 bg-white p-3 shadow-sm sm:p-4">
            <div class="grid grid-cols-1 gap-3 lg:grid-cols-4 lg:items-center">
                // sorteer formulier
                <form
                    method="GET"
                    action="{{ route('technician.materials.index') }}"
                    class="js-preserve-scroll min-w-0 lg:col-span-1"
                >
                    <select
                        name="sort"
                        onchange="this.form.submit()"
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                    >
                        <option value="">
                            Sorteer op naam
                        </option>

                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>
                            A-Z
                        </option>

                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>
                            Z-A
                        </option>
                    </select>
                </form>
// zoekbalk
                <div class="min-w-0 lg:col-span-3">
                    <x-search-bar
                        id="global-material-search"
                        placeholder="Zoeken op naam, categorie of voorraadstatus..."
                        value="{{ request('search') }}"
                        endpoint="{{ route('api.materials.search') }}"
                    />
                </div>
            </div>
        </section>
// categorie filter knoppen
        <div class="-mx-4 overflow-x-auto px-4 pb-1 sm:mx-0 sm:px-0">
            <div class="flex min-w-max gap-3 sm:min-w-0 sm:flex-wrap">
                <button
                    type="button"
                    data-category-filter="all"
                    class="js-category-filter whitespace-nowrap rounded-full bg-[#0F4C81] px-5 py-2 text-xs font-medium text-white shadow-sm transition"
                >
                    Alles
                </button>

                @foreach($categories as $category)
                    <button
                        type="button"
                        data-category-filter="{{ $category }}"
                        class="js-category-filter whitespace-nowrap rounded-full bg-gray-100 px-5 py-2 text-xs font-medium text-gray-600 transition hover:bg-gray-200"
                    >
                        {{ $category }}
                    </button>
                @endforeach
            </div>
        </div>
// materialen grid
        <div
            id="materials"
            class="grid min-w-0 grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4"
        >
            @forelse($materials as $material)
                @php
                    $localStock = $material->stocks->first();
                    $stock = $localStock?->stock ?? 0;
                    $minimumStock = $localStock?->minimum_stock ?? 0;

                    if ($stock <= 0) {
                        $stockStatus = 'geen voorraad';
                    } elseif ($stock <= $minimumStock) {
                        $stockStatus = 'lage voorraad';
                    } else {
                        $stockStatus = 'beschikbaar';
                    }

                    $searchText = collect([
                        $material->name,
                        $material->category,
                        $stock,
                        $stockStatus,
                    ])->filter()->implode(' ');
                @endphp
// materiaal item
                <div
                    class="js-material-item min-w-0"
                    data-category="{{ $material->category }}"
                    data-search="{{ $searchText }}"
                >
                    <x-material-card :material="$material" />
                </div>
            @empty
            // geen materialen gevonden
                <div class="col-span-full rounded-xl border bg-white py-8 text-center text-gray-500 shadow-sm">
                    Geen materialen gevonden.
                </div>
            @endforelse
        </div>
// lege staat voor geen resultaten
        <div
            id="materials-empty-state"
            class="mt-4 hidden rounded-xl border bg-white py-8 text-center text-gray-500 shadow-sm"
        >
            Geen materialen gevonden voor deze zoekterm of categorie.
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const scrollKey = 'technician-materials-scroll-position';
            const savedScroll = sessionStorage.getItem(scrollKey);

            const searchInput = document.getElementById('global-material-search');
            const materialItems = document.querySelectorAll('.js-material-item');
            const categoryButtons = document.querySelectorAll('.js-category-filter');
            const emptyState = document.getElementById('materials-empty-state');

            let selectedCategory = 'all';
// scrollpositie herstellen
            if (savedScroll !== null) {
                requestAnimationFrame(function () {
                    window.scrollTo(0, parseInt(savedScroll, 10));

                    setTimeout(function () {
                        window.scrollTo(0, parseInt(savedScroll, 10));
                        sessionStorage.removeItem(scrollKey);
                    }, 100);
                });
            }
// scrollpositie opslaan bij formulierverzending
            document.querySelectorAll('.js-preserve-scroll').forEach(function (form) {
                form.addEventListener('submit', function () {
                    sessionStorage.setItem(scrollKey, window.scrollY.toString());
                });
            });
// normaliseer tekst voor fuzzy matching
            function normalizeText(value) {
                return (value || '')
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, ' ')
                    .trim();
            }
// levenshtein afstand berekenen
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
// bepaal toegestane afstand op basis van woordlengte
            function allowedDistance(word) {
                if (word.length <= 5) {
                    return 1;
                }

                if (word.length <= 9) {
                    return 2;
                }

                return 3;
            }
// controleer of een querywoord overeenkomt met een lijst van tekstwoorden
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

                    const distance = allowedDistance(queryWord);
                    const queryLength = queryWord.length;
                    const textLength = textWord.length;

                    if (levenshtein(queryWord, textWord) <= distance) {
                        return true;
                    }

                    const minWindowLength = queryLength;
                    const maxWindowLength = Math.min(textLength, queryLength + 1);

                    for (let windowLength = minWindowLength; windowLength <= maxWindowLength; windowLength++) {
                        for (let start = 0; start <= textLength - windowLength; start++) {
                            const part = textWord.substring(start, start + windowLength);

                            if (levenshtein(queryWord, part) <= distance) {
                                return true;
                            }
                        }
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

            function updateCategoryButtons(activeButton) {
                categoryButtons.forEach(function (button) {
                    button.classList.remove('bg-[#0F4C81]', 'text-white', 'shadow-sm');
                    button.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                });

                activeButton.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                activeButton.classList.add('bg-[#0F4C81]', 'text-white', 'shadow-sm');
            }

            function applyMaterialFilters() {
                const searchValue = searchInput ? searchInput.value : '';
                let visibleCount = 0;

                materialItems.forEach(function (item) {
                    const matchesCategory = selectedCategory === 'all' || item.dataset.category === selectedCategory;
                    const matchesSearch = fuzzyMatches(searchValue, item.dataset.search);

                    if (matchesCategory && matchesSearch) {
                        item.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        item.classList.add('hidden');
                    }
                });

                if (emptyState) {
                    if (visibleCount === 0 && materialItems.length > 0) {
                        emptyState.classList.remove('hidden');
                    } else {
                        emptyState.classList.add('hidden');
                    }
                }
            }

            categoryButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    selectedCategory = button.dataset.categoryFilter;
                    updateCategoryButtons(button);
                    applyMaterialFilters();
                });
            });

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    applyMaterialFilters();
                });
            }

            document.querySelectorAll('.js-add-to-cart').forEach(function (form) {
                form.addEventListener('submit', async function (event) {
                    event.preventDefault();

                    const button = form.querySelector('button[type="submit"]');
                    const originalText = button.textContent.trim();

                    button.disabled = true;
                    button.textContent = 'Toevoegen...';

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        const data = await response.json();

                        if (! response.ok || ! data.success) {
                            throw new Error(data.message || 'Er ging iets mis.');
                        }

                        button.textContent = 'Toegevoegd ✓';

                        const cartCountElement = document.getElementById('cart-count');

                        if (cartCountElement && data.cart_count !== undefined) {
                            cartCountElement.textContent = data.cart_count;

                            if (data.cart_count > 0) {
                                cartCountElement.classList.remove('hidden');
                            }
                        }

                        setTimeout(function () {
                            button.disabled = false;
                            button.textContent = originalText;
                        }, 900);
                    } catch (error) {
                        button.disabled = false;
                        button.textContent = originalText;
                        alert(error.message);
                    }
                });
            });

            applyMaterialFilters();
        });

        function toggleRecommendations()
        {
            const container = document.getElementById('recommendationsContainer');
            const icon = document.getElementById('recommendationIcon');

            if (! container || ! icon) {
                return;
            }

            if (container.classList.contains('hidden')) {
                container.classList.remove('hidden');
                icon.textContent = '▲ Verberg';
            } else {
                container.classList.add('hidden');
                icon.textContent = '▼ Toon aanbevelingen';
            }
        }
    </script>
</x-app-layout>
