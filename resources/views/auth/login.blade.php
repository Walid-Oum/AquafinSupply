{{--
    Pagina: Login

    Doel:
    Laat gebruikers aanmelden op het
    Aquafin Supply platform.

    Functionaliteiten:
    - Inloggen met e-mailadres en wachtwoord
    - Onthoud mij functionaliteit
    - Wachtwoord vergeten link
    - Weergave van fout- en succesmeldingen

    Gebruikersrol:
    - Admin
    - Technieker
    - Magazijn

    Opmerking:
    Na succesvolle authenticatie wordt de
    gebruiker doorgestuurd naar zijn of haar
    rolgebonden dashboard.
--}}
<x-guest-layout>
    <div class="mb-10 text-center">
        <h1 class="text-4xl font-extrabold text-[#0F4C81] sm:text-5xl">
            Aquafin
        </h1>

        <p class="mt-2 text-gray-500">
            Supply App
        </p>
    </div>

    <x-breeze.auth-session-status
        class="mb-4"
        :status="session('status')"
    />

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-300 bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-lg border border-red-300 bg-red-100 px-4 py-3 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-300 bg-red-100 px-4 py-3 text-red-800">
            {{ $errors->first() }}
        </div>
    @endif
    {{-- Aanmeldformulier voor gebruikers --}}
    <form method="POST" action="{{ route('login') }}">
        @csrf
        {{-- Invoer van e-mailadres --}}
        <div>
            <label for="email" class="mb-2 block font-semibold text-gray-700">
                Email
            </label>

            <x-breeze.text-input
                id="email"
                class="w-full rounded-xl px-4 py-3"
                type="email"
                name="email"
                :value="old('email')"
                autocomplete="username"
                required
                autofocus
            />
        </div>
        {{-- Invoer van wachtwoord --}}
        <div class="mt-5">
            <label for="password" class="mb-2 block font-semibold text-gray-700">
                Wachtwoord
            </label>

            <x-breeze.text-input
                id="password"
                class="w-full rounded-xl px-4 py-3"
                type="password"
                name="password"
                autocomplete="current-password"
                required
            />
        </div>
        {{-- Extra loginopties --}}
        <div class="mt-5 flex items-center justify-between gap-4">
            <label for="remember" class="flex items-center gap-2">
                <input
                    id="remember"
                    type="checkbox"
                    name="remember"
                    class="rounded border-gray-300 text-[#0F4C81] focus:ring-[#0F4C81]"
                >

                <span class="text-gray-600">
                    Onthoud mij
                </span>
            </label>

            <a
                href="{{ route('password.request') }}"
                class="font-semibold text-[#1E6BA8] hover:underline"
            >
                Wachtwoord vergeten?
            </a>
        </div>

        <button
            type="submit"
            class="mt-6 w-full rounded-xl bg-[#0F4C81] py-3 font-semibold text-white transition hover:bg-[#1E6BA8]"
        >
            Inloggen
        </button>
    </form>
</x-guest-layout>
