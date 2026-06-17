@props([
    'id',
    'name' => 'search',
    'value' => '',
    'placeholder' => 'Zoeken...',
    'endpoint' => null,
    'targetSelector' => null,
    'emptyStateId' => null,
])

<div
    class="relative w-full max-w-md"
    data-search-bar
    data-endpoint="{{ $endpoint }}"
    data-target-selector="{{ $targetSelector }}"
    data-empty-state-id="{{ $emptyStateId }}"
>
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="text"
        value="{{ $value }}"
        autocomplete="off"
        placeholder="{{ $placeholder }}"
        data-search-input
        class="w-full rounded border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
    >

    <ul
        data-search-results
        class="absolute z-50 mt-1 hidden max-h-60 w-full divide-y divide-gray-100 overflow-y-auto rounded border border-gray-200 bg-white shadow-xl"
    ></ul>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
                if (word.length <= 3) {
                    return 1;
                }

                if (word.length <= 7) {
                    return 2;
                }

                if (word.length <= 12) {
                    return 3;
                }

                return 4;
            }

            function wordMatches(queryWord, textWords) {
                if (queryWord.length === 0) {
                    return true;
                }

                for (const textWord of textWords) {
                    if (textWord.includes(queryWord)) {
                        return true;
                    }

                    if (queryWord.length >= 3 && levenshtein(queryWord, textWord) <= allowedDistance(queryWord)) {
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

            function filterTargets(searchBar, query) {
                const targetSelector = searchBar.dataset.targetSelector;
                const emptyStateId = searchBar.dataset.emptyStateId;

                if (! targetSelector) {
                    return;
                }

                const targets = document.querySelectorAll(targetSelector);
                const emptyState = emptyStateId ? document.getElementById(emptyStateId) : null;

                let visibleCount = 0;

                targets.forEach(function (target) {
                    const searchableText = target.dataset.search || target.textContent;
                    const matchesSearch = fuzzyMatches(query, searchableText);

                    if (matchesSearch) {
                        target.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        target.classList.add('hidden');
                    }
                });

                if (emptyState) {
                    if (visibleCount === 0 && targets.length > 0) {
                        emptyState.classList.remove('hidden');
                    } else {
                        emptyState.classList.add('hidden');
                    }
                }
            }

            function renderSuggestions(resultsList, suggestions, input) {
                resultsList.innerHTML = '';

                if (suggestions.length === 0) {
                    resultsList.innerHTML = '<li class="px-4 py-2 text-sm italic text-gray-400">Geen resultaten...</li>';
                    resultsList.classList.remove('hidden');
                    return;
                }

                suggestions.forEach(function (item) {
                    const li = document.createElement('li');

                    li.className = 'flex cursor-pointer items-center justify-between gap-3 px-4 py-2 text-sm hover:bg-gray-100';

                    li.innerHTML = `
                        <div>
                            <p class="font-medium text-gray-700">${item.label}</p>
                            ${item.subtitle ? `<p class="text-xs text-gray-400">${item.subtitle}</p>` : ''}
                        </div>

                        ${item.badge ? `<span class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-500">${item.badge}</span>` : ''}
                    `;

                    li.addEventListener('click', function () {
                        input.value = item.label;
                        resultsList.classList.add('hidden');

                        input.dispatchEvent(new Event('input'));

                        if (item.url) {
                            window.location.href = item.url;
                        }
                    });

                    resultsList.appendChild(li);
                });

                resultsList.classList.remove('hidden');
            }

            document.querySelectorAll('[data-search-bar]').forEach(function (searchBar) {
                const input = searchBar.querySelector('[data-search-input]');
                const resultsList = searchBar.querySelector('[data-search-results]');
                const endpoint = searchBar.dataset.endpoint;

                let searchTimeout = null;

                input.addEventListener('input', function () {
                    const query = input.value.trim();

                    filterTargets(searchBar, query);

                    clearTimeout(searchTimeout);

                    if (! endpoint || query.length < 2) {
                        resultsList.innerHTML = '';
                        resultsList.classList.add('hidden');
                        return;
                    }

                    searchTimeout = setTimeout(async function () {
                        try {
                            const response = await fetch(`${endpoint}?q=${encodeURIComponent(query)}`);
                            const suggestions = await response.json();

                            renderSuggestions(resultsList, suggestions, input);
                        } catch (error) {
                            console.error('Fout bij ophalen zoekresultaten:', error);
                        }
                    }, 250);
                });

                document.addEventListener('click', function (event) {
                    if (! input.contains(event.target) && ! resultsList.contains(event.target)) {
                        resultsList.classList.add('hidden');
                    }
                });

                filterTargets(searchBar, input.value.trim());
            });
        });
    </script>
@endonce

