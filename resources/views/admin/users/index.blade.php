{{--
    Pagina: Gebruikersbeheer

    Doel:
    Overzicht van alle gebruikers binnen het
    Aquafin Supply systeem.

    Functionaliteiten:
    - Overzicht van alle gebruikers
    - Zoeken op naam of e-mailadres
    - Filteren op gebruikersrol
    - Nieuwe gebruikers aanmaken
    - Bestaande gebruikers aanpassen
    - Weergave voor desktop en mobiel

    Gebruikersrol:
    - Admin
--}}

<x-app-layout>
    <div class="min-w-0 max-w-full space-y-6 overflow-x-hidden">
        {{-- HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <x-page-header title="Gebruikersbeheer" />

                <p class="mt-1 text-sm text-gray-600 sm:text-base">
                    Beheer gebruikers, rollen, locaties en accountstatussen.
                </p>
            </div>

            <a href="{{ route('admin.users.create') }}" class="w-full sm:w-auto">
                <x-button class="w-full justify-center sm:w-auto">
                    + Nieuwe gebruiker
                </x-button>
            </a>
        </div>

        {{-- ZOEKEN EN FILTEREN --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="w-full lg:max-w-sm">
                    <input
                        type="text"
                        id="user-table-search"
                        autocomplete="off"
                        placeholder="Gebruiker zoeken..."
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                    >
                </div>

                <form
                    method="GET"
                    action="{{ route('admin.users.index') }}"
                    class="flex w-full flex-col gap-3 sm:flex-row lg:w-auto"
                >
                    <select
                        name="role"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20 sm:w-auto"
                    >
                        <option value="">Alle rollen</option>

                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>
                            Administrator
                        </option>

                        <option value="technieker" {{ request('role') == 'technieker' ? 'selected' : '' }}>
                            Technieker
                        </option>

                        <option value="magazijn" {{ request('role') == 'magazijn' ? 'selected' : '' }}>
                            Magazijnmedewerker
                        </option>
                    </select>

                    <x-button class="w-full justify-center sm:w-auto">
                        Filter
                    </x-button>

                    <a
                        href="{{ route('admin.users.index') }}"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
                    >
                        Reset
                    </a>
                </form>
            </div>
        </div>

        {{-- MOBIELE WEERGAVE --}}
        <div class="space-y-4 lg:hidden" id="user-mobile-list">
            @forelse($users as $user)
                <article
                    class="user-card rounded-2xl border border-gray-100 bg-white p-4 shadow-sm"
                    data-name="{{ strtolower($user->name) }}"
                    data-email="{{ strtolower($user->email) }}"
                >
                    <div class="flex flex-col gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 user-name-mobile">
                                {{ $user->name }}
                            </h2>

                            <p class="break-all text-sm text-gray-500 user-email-mobile">
                                {{ $user->email }}
                            </p>
                        </div>

                        <div class="grid gap-2 text-sm text-gray-700">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-gray-500">Rol</span>

                                <span class="rounded-full px-3 py-1 text-xs font-semibold
                                    {{ $user->role === 'admin' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                                    {{ $user->role === 'magazijn' ? 'bg-sky-50 text-sky-700 border border-sky-200' : '' }}
                                    {{ $user->role === 'technieker' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <span class="text-gray-500">Status</span>

                                @if($user->is_active)
                                    <span class="rounded-full border border-green-200 bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                                        Actief
                                    </span>
                                @else
                                    <span class="rounded-full border border-red-200 bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">
                                        Inactief
                                    </span>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('admin.users.edit', $user->id) }}" class="pt-2">
                            <x-button class="w-full justify-center">
                                Aanpassen
                            </x-button>
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-gray-100 bg-white p-6 text-center text-gray-500 shadow-sm">
                    Geen gebruikers gevonden.
                </div>
            @endforelse

            <div
                id="no-users-found-mobile"
                class="hidden rounded-2xl border border-gray-100 bg-white p-6 text-center text-gray-500 shadow-sm"
            >
                Geen gebruikers gevonden die voldoen aan de zoekterm.
            </div>
        </div>

        {{-- DESKTOPWEERGAVE --}}
        <div class="hidden overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm lg:block">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] divide-y divide-gray-200 text-left" id="user-table">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Naam</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">E-mailadres</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Rol</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-gray-500">Acties</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white" id="user-table-body">
                    @foreach($users as $user)
                        <tr class="user-row transition-colors duration-150 hover:bg-gray-50/70">
                            <td class="user-name whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $user->name }}
                            </td>

                            <td class="user-email whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $user->email }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold leading-5
                                        {{ $user->role === 'admin' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                                        {{ $user->role === 'magazijn' ? 'bg-sky-50 text-sky-700 border border-sky-200' : '' }}
                                        {{ $user->role === 'technieker' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                @if($user->is_active)
                                    <span class="inline-flex rounded-full border border-green-200 bg-green-100 px-3 py-1 text-xs font-semibold leading-5 text-green-800">
                                            Actief
                                        </span>
                                @else
                                    <span class="inline-flex rounded-full border border-red-200 bg-red-100 px-3 py-1 text-xs font-semibold leading-5 text-red-800">
                                            Inactief
                                        </span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                <a
                                    href="{{ route('admin.users.edit', $user->id) }}"
                                    class="inline-flex items-center gap-1 text-[#1E6BA8] transition-colors hover:text-[#0F4C81]"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Aanpassen
                                </a>
                            </td>
                        </tr>
                    @endforeach

                    <tr id="no-users-found-row" class="hidden">
                        <td colspan="5" class="bg-gray-50 px-6 py-8 text-center italic text-gray-500">
                            Geen gebruikers gevonden die voldoen aan de zoekterm.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Zoekfunctionaliteit met fuzzy matching --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('user-table-search');
            const tableRows = document.querySelectorAll('.user-row');
            const mobileCards = document.querySelectorAll('.user-card');
            const noResultsRow = document.getElementById('no-users-found-row');
            const noResultsMobile = document.getElementById('no-users-found-mobile');

            function normalizeText(text) {
                if (!text) return '';
                return text.toLowerCase()
                    .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
                    .replace(/[^a-z0-9]/g, ' ')
                    .replace(/\s+/g, ' ')
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

            function isUserMatch(name, email, query) {
                const flatQuery = query.replace(/ /g, '');
                const flatName = name.replace(/ /g, '');
                const flatEmail = email.replace(/ /g, '');

                if (
                    name.includes(query) ||
                    email.includes(query) ||
                    flatName.includes(flatQuery) ||
                    flatEmail.includes(flatQuery)
                ) {
                    return true;
                }

                if (flatQuery.length < 3) {
                    return false;
                }

                const allowedDistance = getAllowedDistance(flatQuery);

                if (
                    levenshtein(flatQuery, flatName) <= allowedDistance ||
                    levenshtein(flatQuery, flatEmail) <= allowedDistance
                ) {
                    return true;
                }

                const queryWords = query.split(' ');
                const nameWords = name.split(' ');

                for (const qWord of queryWords) {
                    for (const nWord of nameWords) {
                        if (
                            nWord.includes(qWord) ||
                            qWord.includes(nWord) ||
                            (qWord.length >= 3 && levenshtein(qWord, nWord) <= getAllowedDistance(qWord))
                        ) {
                            return true;
                        }
                    }
                }

                return false;
            }

            if (!searchInput) return;

            searchInput.addEventListener('input', function () {
                const query = normalizeText(this.value);
                let visibleTableCount = 0;
                let visibleMobileCount = 0;

                if (query === '') {
                    tableRows.forEach(row => row.style.display = '');
                    mobileCards.forEach(card => card.classList.remove('hidden'));
                    noResultsRow?.classList.add('hidden');
                    noResultsMobile?.classList.add('hidden');
                    return;
                }

                tableRows.forEach(row => {
                    const name = normalizeText(row.querySelector('.user-name')?.textContent || '');
                    const email = normalizeText(row.querySelector('.user-email')?.textContent || '');

                    if (isUserMatch(name, email, query)) {
                        row.style.display = '';
                        visibleTableCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                mobileCards.forEach(card => {
                    const name = normalizeText(card.querySelector('.user-name-mobile')?.textContent || '');
                    const email = normalizeText(card.querySelector('.user-email-mobile')?.textContent || '');

                    if (isUserMatch(name, email, query)) {
                        card.classList.remove('hidden');
                        visibleMobileCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                if (visibleTableCount === 0) {
                    noResultsRow?.classList.remove('hidden');
                } else {
                    noResultsRow?.classList.add('hidden');
                }

                if (visibleMobileCount === 0) {
                    noResultsMobile?.classList.remove('hidden');
                } else {
                    noResultsMobile?.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
