<section>
   <header class="mb-6">

    <h2 class="text-2xl font-bold text-[#0F4C81]">
        Profielgegevens
    </h2>

    <p class="text-gray-500 mt-2">
        Werk uw persoonlijke gegevens bij.
    </p>

</header>
    

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-breeze.input-label for="name" :value="__('Name')" />
            <x-breeze.text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-breeze.input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-breeze.input-label for="email" value="E-mailadres" />

            <x-breeze.text-input
                id="email"
                type="email"
                class="mt-1 block w-full cursor-not-allowed bg-gray-100 text-gray-500"
                :value="$user->email"
                disabled
            />

            <p class="mt-2 text-sm text-gray-500">
                Je e-mailadres kan enkel door een administrator gewijzigd worden.
            </p>
        </div>

        <div class="flex items-center gap-4">
            <x-button>
    Opslaan
</x-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
