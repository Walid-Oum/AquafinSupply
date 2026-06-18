{{--
    Pagina: Gebruiker aanpassen

    Doel:
    Laat een administrator toe om bestaande
    gebruikersgegevens te wijzigen.

    Gebruikersrol:
    - Admin
--}}

<x-app-layout>
    <div class="mx-auto w-full max-w-4xl space-y-6">
        <a
            href="{{ route('admin.users.index') }}"
            class="inline-flex items-center gap-1 text-sm text-gray-500 transition-colors hover:text-gray-700"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Terug naar overzicht
        </a>

        <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-gray-50 px-4 py-4 sm:px-6">
                <h3 class="text-xl font-bold text-gray-800">
                    Gebruiker aanpassen
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Wijzig de accountgegevens van
                    <span class="font-semibold text-gray-700">{{ $user->name }}</span>.
                </p>
            </div>

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-5 p-4 sm:p-6">
                @csrf
                @method('PUT')

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Naam
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm transition-all focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                            value="{{ old('name', $user->name) }}"
                            required
                        >

                        @error('name')
                        <p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            E-mailadres
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm transition-all focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                            value="{{ old('email', $user->email) }}"
                            required
                        >

                        @error('email')
                        <p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        @if(Auth::id() != $user->id)
                            <label class="mb-2 block text-sm font-semibold text-gray-700">
                                Rol
                            </label>

                            <select
                                name="role"
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                                required
                            >
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                        {{ ucfirst($role) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('role')
                            <p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>
                            @enderror
                        @else
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                                Je kunt je eigen rol niet wijzigen.
                            </div>
                        @endif
                    </div>

                    <div>
                        <label for="location_id" class="mb-2 block text-sm font-semibold text-gray-700">
                            Locatie
                        </label>

                        <select
                            id="location_id"
                            name="location_id"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                        >
                            <option value="">Geen locatie</option>

                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" @selected(old('location_id', $user->location_id) == $location->id)>
                                    {{ $location->province }} - {{ $location->name }} ({{ $location->city }})
                                </option>
                            @endforeach
                        </select>

                        @error('location_id')
                        <p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        @if(Auth::id() != $user->id)
                            <label class="mb-2 block text-sm font-semibold text-gray-700">
                                Accountstatus
                            </label>

                            <select
                                name="is_active"
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                            >
                                <option value="1" {{ $user->is_active ? 'selected' : '' }}>
                                    Actief
                                </option>

                                <option value="0" {{ !$user->is_active ? 'selected' : '' }}>
                                    Inactief
                                </option>
                            </select>
                        @else
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                                Je kunt je eigen accountstatus niet wijzigen.
                            </div>

                            <input type="hidden" name="is_active" value="1">
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-gray-600">
                                Wachtwoord wijzigen optioneel
                            </label>

                            <p class="mb-4 text-xs text-gray-400">
                                Laat deze velden leeg als je het huidige wachtwoord wilt behouden.
                            </p>

                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                                        Nieuw wachtwoord
                                    </label>

                                    <input
                                        type="password"
                                        name="password"
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm transition-all focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                                        placeholder="Nieuw wachtwoord"
                                    >

                                    @error('password')
                                    <p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                                        Nieuw wachtwoord bevestigen
                                    </label>

                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm transition-all focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                                        placeholder="Herhaal nieuw wachtwoord"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
                    >
                        Annuleren
                    </a>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-[#0F4C81] px-5 py-3 font-semibold text-white shadow transition hover:bg-[#1E6BA8] sm:w-auto"
                    >
                        Opslaan
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
