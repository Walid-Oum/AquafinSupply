
<x-app-layout>
<div class="container mx-auto px-6 py-8">
    
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Terug naar overzicht
        </a>
    </div>

    <div class="max-w-2xl bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-gray-800 text-xl font-bold">Nieuwe Gebruiker Aanmaken</h3>
            <p class="text-gray-500 text-xs mt-0.5">Voeg een nieuwe werknemer toe aan het Aquafin Supply systeem.</p>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Volledige Naam</label>
                <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" value="{{ old('name') }}" placeholder="Bijv. Jan Peeters" required>
                @error('name') <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">E-mailadres</label>
                <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" value="{{ old('email') }}" placeholder="username@aquafin.be" required>
                @error('email') <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Rol binnen Aquafin </label>
                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                    <option value="" disabled selected>Selecteer een rol...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                @error('role') <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Tijdelijk Wachtwoord</label>
                <input type="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Minimaal 8 tekens" required>
                @error('password') <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Bevestig Wachtwoord</label>
                <input type="password" name="password_confirmation" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Herhaal het wachtwoord" required>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors duration-150">Annuleren</a>
                <button type="submit" class="bg-[#0F4C81] hover:bg-[#1E6BA8] text-white font-semibold py-2 px-5 rounded-lg transition-all duration-150 shadow">Gebruiker Opslaan</button>
            </div>
        </form>
    </div>
</div>

</x-app-layout>