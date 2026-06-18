{{--
    Pagina: Wachtwoord wijzigen

    Doel:
    Verplicht scherm waarmee een gebruiker
    een nieuw wachtwoord moet instellen bij
    de eerste aanmelding.

    Functionaliteiten:
    - Nieuw wachtwoord instellen
    - Bevestiging van wachtwoord
    - Validatie van wachtwoordregels

    Gebruikersrol:
    - Admin
    - Technieker
    - Magazijn

    Opmerking:
    Deze pagina wordt gebruikt wanneer een
    gebruiker verplicht is zijn tijdelijk
    wachtwoord te vervangen.
--}}
<x-app-layout>
    <div class="mx-auto max-w-xl p-8">
        <div class="rounded-xl bg-white p-6 shadow">
            <h1 class="text-2xl font-bold text-gray-900">
                Stel je nieuw wachtwoord in
            </h1>

            <p class="mt-2 text-gray-600">
                Om veiligheidsredenen moet je bij je eerste aanmelding een nieuw wachtwoord kiezen.
            </p>
            {{-- Formulier voor het instellen van een nieuw wachtwoord --}}
            <form method="POST" action="{{ route('password.change.update') }}" class="mt-6 space-y-4">
                @csrf
                @method('PATCH')
                {{-- Invoer van het nieuwe wachtwoord --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700">
                        Nieuw wachtwoord
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        required
                    >

                    @error('password')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </div>
                {{-- Bevestiging van het nieuwe wachtwoord --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700">
                        Bevestig nieuw wachtwoord
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                        required
                    >
                </div>

                <x-button>
                    Wachtwoord opslaan
                </x-button>
            </form>
        </div>
    </div>
</x-app-layout>
