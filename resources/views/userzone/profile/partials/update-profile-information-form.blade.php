<section>
    <header class="mb-6">
        <h2 class="text-2xl font-bold text-[#0F4C81]">
            Profielgegevens
        </h2>

        <p class="mt-2 text-gray-500">
            Werk je persoonlijke gegevens bij.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- NAAM --}}
        <div>
            <x-breeze.input-label for="name" value="Naam" />

            <x-breeze.text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
            />

            <x-breeze.input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- E-MAILADRES --}}
        <div>
            <p class="mb-2 block text-sm font-semibold text-gray-700">
                E-mailadres
            </p>

            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                {{ $user->email }}
            </div>

            <p class="mt-2 text-sm text-gray-500">
                @if($user->role === 'admin')
                    E-mailadressen kunnen aangepast worden via gebruikersbeheer.
                @else
                    Je e-mailadres kan enkel door een administrator gewijzigd worden.
                @endif
            </p>
        </div>

        {{-- ACTIES --}}
        <div class="flex items-center gap-4">
            <x-button type="submit">
                Opslaan
            </x-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >
                    Opgeslagen.
                </p>
            @endif
        </div>
    </form>
</section>
