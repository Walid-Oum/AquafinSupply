
<x-app-layout>

    <x-page-header title="Gebruikersbeheer" />

<div class="mb-4 flex justify-between items-center">

    <a href="{{ route('admin.users.create') }}">
        <x-button>
            + Nieuwe gebruiker
        </x-button>
    </a>

    <div class="flex gap-4 items-center">

        <div class="relative">
            <input
                type="text"
                id="user-table-search"
                autocomplete="off"
                placeholder="Gebruiker zoeken..."
                class="border rounded px-3 py-2 w-64 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F4C81]">
        </div>

        <form
            method="GET"
            action="{{ route('admin.users.index') }}"
            class="flex gap-2">

            <select
                name="role"
                class="border rounded px-3 py-2 text-sm">

                <option value="">
                    Alle rollen
                </option>

                <option
                    value="admin"
                    {{ request('role') == 'admin' ? 'selected' : '' }}>
                    Administrator
                </option>

                <option
                    value="technieker"
                    {{ request('role') == 'technieker' ? 'selected' : '' }}>
                    Technieker
                </option>

                <option
                    value="magazijn"
                    {{ request('role') == 'magazijn' ? 'selected' : '' }}>
                    Magazijnmedewerker
                </option>

            </select>

            <x-button>
                Filter
            </x-button>

            <a
                href="{{ route('admin.users.index') }}"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-400 transition">

                Reset

            </a>

        </form>

    </div>

</div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left" id="user-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Naam</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">E-mailadres</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100" id="user-table-body">
                        @foreach($users as $user)
                            <tr class="user-row hover:bg-gray-50/70 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 user-name">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 user-email">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $user->role === 'admin' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                                        {{ $user->role === 'magazijn' ? 'bg-sky-50 text-sky-700 border border-sky-200' : '' }}
                                        {{ $user->role === 'technieker' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->is_active)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                            Actief
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">
                                            Inactief
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="text-[#1E6BA8] hover:text-[#0F4C81] inline-flex items-center gap-1 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Aanpassen
                                        </a>
                                        
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <tr id="no-users-found-row" class="hidden">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic bg-gray-50">
                                Geen gebruikers gevonden die voldoen aan de zoekterm.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('user-table-search');
    const tableRows = document.querySelectorAll('.user-row');
    const noResultsRow = document.getElementById('no-users-found-row');

    function normalizeText(text) {
        if (!text) return '';
        return text.toLowerCase()
            .normalize("NFD").replace(/[\u0300-\u036f]/g, "") // é -> e
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
        const query = normalizeText(this.value);
        let visibleCount = 0;

        if (query === '') {
            tableRows.forEach(row => row.style.display = '');
            noResultsRow.classList.add('hidden');
            return;
        }

        const flatQuery = query.replace(/ /g, '');

        tableRows.forEach(row => {
            const name = normalizeText(row.querySelector('.user-name').textContent);
            const email = normalizeText(row.querySelector('.user-email').textContent);
            
            const flatName = name.replace(/ /g, '');
            const flatEmail = email.replace(/ /g, '');

            let isMatch = false;

            // 1. Directe match of match zonder spaties (Beide HEAD & main hersteld en samengevoegd)
            if (name.includes(query) || email.includes(query) || flatName.includes(flatQuery) || flatEmail.includes(flatQuery)) {
                isMatch = true;
            }

            // 2. Fuzzy match met Levenshtein
            if (!isMatch && flatQuery.length >= 3) {
                const allowedDistance = getAllowedDistance(flatQuery);
                
                if (levenshtein(flatQuery, flatName) <= allowedDistance || levenshtein(flatQuery, flatEmail) <= allowedDistance) {
                    isMatch = true;
                }

                if (!isMatch) {
                    const queryWords = query.split(' ');
                    const nameWords = name.split(' ');

                    queryWords.forEach(qWord => {
                        nameWords.forEach(nWord => {
                            if (nWord.includes(qWord) || qWord.includes(nWord)) {
                                isMatch = true;
                            } else if (qWord.length >= 3 && levenshtein(qWord, nWord) <= getAllowedDistance(qWord)) {
                                isMatch = true;
                            }
                        });
                    });
                }
            }

            // Weergave bijwerken en teller bijhouden
            if (isMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Toon melding als alles verborgen is
        if (visibleCount === 0) {
            noResultsRow.classList.remove('hidden');
        } else {
            noResultsRow.classList.add('hidden');
        }
    });
});
</script>
</x-app-layout>