<x-app-layout>

    <x-page-header title="Gebruikersbeheer" />
     <p class="text-gray-500 text-sm mb-6">
        Overzicht van alle actieve accounts binnen Aquafin Supply
    </p>
 <div class="px-6 -mt-4 mb-6 flex justify-between items-center">

    <p class="text-gray-500 text-sm">
       
    </p>

    <a
        href="{{ route('admin.users.create') }}"
        class="bg-[#0F4C81] hover:bg-[#1E6BA8] text-white font-semibold py-2.5 px-5 rounded-lg transition-all duration-200 shadow-md flex items-center gap-2 text-sm">

        Nieuwe Gebruiker

    </a>

</div>

<div class="container mx-auto px-6">

    <div class="flex justify-end mb-6">

        <div class="flex items-center gap-4">

            <form
                method="GET"
                action="{{ route('admin.users.index') }}"
                class="flex items-center gap-2">

                <select
                    name="role"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm">

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
                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">

                    Reset

                </a>

            </form>

            <div class="relative">

                <input
                    type="text"
                    id="user-table-search"
                    autocomplete="off"
                    placeholder="Gebruiker zoeken..."
                    class="border border-gray-300 rounded-lg px-3 py-2 w-64 text-sm focus:outline-none focus:ring-2 focus:ring-[#0F4C81] focus:border-[#0F4C81] transition-all shadow-sm">

            </div>

        </div>

    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-r-lg mb-6 shadow-sm flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left" id="user-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Naam</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">E-mailadres</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Rol</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Acties</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-[#1E6BA8] hover:text-[#0F4C81] inline-flex items-center gap-1 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Aanpassen
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('user-table-search');
    const tableRows = document.querySelectorAll('.user-row');

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();

        tableRows.forEach(row => {
            const name = row.querySelector('.user-name').textContent.toLowerCase();
            const email = row.querySelector('.user-email').textContent.toLowerCase();

            if (name.includes(query) || email.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>
</x-app-layout>