
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
            <h3 class="text-gray-800 text-xl font-bold">Gebruiker Aanpassen</h3>
            <p class="text-gray-500 text-xs mt-0.5">Wijzig de accountgegevens van <span class="font-semibold text-gray-700">{{ $user->name }}</span>.</p>
        </div>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Naam</label>
                <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" value="{{ old('name', $user->name) }}" required>
                @error('name') <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">E-mailadres</label>
                <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" value="{{ old('email', $user->email) }}" required>
                @error('email') <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
<<<<<<< HEAD
              

@if(Auth::id() != $user->id)

<label class="block text-gray-700 text-sm font-semibold mb-2">
    Rol
</label>

<select
    name="role"
    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
    required>

    @foreach($roles as $role)
        <option value="{{ $role }}"
            {{ old('role', $user->role) == $role ? 'selected' : '' }}>
            {{ ucfirst($role) }}
        </option>
    @endforeach

</select>

@error('role')
<p class="text-rose-500 text-xs mt-1 font-medium">
    {{ $message }}
</p>
@enderror

@else

<p class="text-gray-500 text-sm">
    U kunt uw eigen rol niet wijzigen.
</p>

@endif

=======
                @if(Auth::id() != $user->id)
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Rol</label>
                    <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role') <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                @else
                    <p class="text-gray-500 text-sm">U kunt uw eigen rol niet wijzigen.</p>
                    <input type="hidden" name="role" value="{{ $user->role }}">
                @endif
>>>>>>> c6d58ff (Feature: admin kan gebruikers deactiveren/activeren via edit pagina)
            </div>

            <div>
                <label for="location_id" class="block text-gray-700 text-sm font-semibold mb-2">
                    Locatie
                </label>

                <select
                    id="location_id"
                    name="location_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                >
                    <option value="">Geen locatie</option>

                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}"
                            @selected(old('location_id', $user->location_id) == $location->id)>
                            {{ $location->province }} - {{ $location->name }} ({{ $location->city }})
                        </option>
                    @endforeach
                </select>

                @error('location_id')
                <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            @if(Auth::id() != $user->id)
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Accountstatus</label>
                    <select name="is_active" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="1" {{ $user->is_active ? 'selected' : '' }}>Actief</option>
                        <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactief</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Actief = gebruiker kan inloggen. Inactief = gebruiker kan niet inloggen.</p>
                </div>
            @else
                <div>
                    <p class="text-gray-500 text-sm">Je kunt je eigen accountstatus niet wijzigen.</p>
                    <input type="hidden" name="is_active" value="1">
                </div>
            @endif

            <div class="mb-4 border-t border-gray-100 pt-4">
                <label class="block text-gray-600 text-xs font-bold mb-1 uppercase tracking-wider">Wachtwoord Wijzigen (Optioneel)</label>
                <p class="text-gray-400 text-xs mb-3">Laat deze velden leeg als je het huidige wachtwoord wilt behouden.</p>
                <input type="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Nieuw wachtwoord">
                @error('password') <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">Nieuw Wachtwoord Bevestigen</label>
                <input type="password" name="password_confirmation" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Herhaal nieuw wachtwoord">
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.users.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors duration-150">Annuleren</a>
                <button type="submit" class="bg-[#0F4C81] hover:bg-[#1E6BA8] text-white font-semibold py-2 px-5 rounded-lg transition-all duration-150 shadow">Opslaan</button>
            </div>
        </form>
    </div>
</div>

</x-app-layout>
