{{--
    Pagina: Nieuwe gebruiker aanmaken

    Doel:
    Laat een administrator toe om een nieuwe gebruiker
    aan te maken binnen het Aquafin Supply systeem.

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
                    Nieuwe gebruiker aanmaken
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Voeg een nieuwe werknemer toe aan het Aquafin Supply systeem.
                </p>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5 p-4 sm:p-6">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Volledige naam
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm transition-all focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                            value="{{ old('name') }}"
                            placeholder="Bijv. Jan Peeters"
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
                            value="{{ old('email') }}"
                            placeholder="username@aquafin.be"
                            required
                        >

                        @error('email')
                        <p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Rol binnen Aquafin
                        </label>

                        <select
                            name="role"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                            required
                        >
                            <option value="" disabled selected>Selecteer een rol...</option>

                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>

                        @error('role')
                        <p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="location_id" class="mb-2 block text-sm font-semibold text-gray-700">
                            Locatie
                        </label>

                        <select
                            id="location_id"
                            name="location_id"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm transition-all focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                            required
                        >
                            <option value="" disabled selected>Kies een locatie</option>

                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>
                                    {{ $location->province }} - {{ $location->name }} ({{ $location->city }})
                                </option>
                            @endforeach
                        </select>

                        @error('location_id')
                        <p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Tijdelijk wachtwoord
                        </label>

                        <input
                            type="password"
                            name="password"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm transition-all focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                            placeholder="Minimaal 8 tekens"
                            required
                        >

                        @error('password')
                        <p class="mt-1 text-xs font-medium text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Bevestig wachtwoord
                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm transition-all focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                            placeholder="Herhaal het wachtwoord"
                            required
                        >
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
                        Gebruiker opslaan
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
