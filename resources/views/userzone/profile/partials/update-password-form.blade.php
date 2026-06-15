<section>
    <header class="mb-6">

    <h2 class="text-2xl font-bold text-[#0F4C81]">
        Wachtwoord wijzigen
    </h2>

    <p class="text-gray-500 mt-2">
        Kies een sterk wachtwoord om uw account te beveiligen.
    </p>

</header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-breeze.input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-breeze.text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-breeze.input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-breeze.input-label for="update_password_password" :value="__('New Password')" />
            <x-breeze.text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-breeze.input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-breeze.input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-breeze.text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-breeze.input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-button>
    Opslaan
</x-button>

            @if (session('status') === 'password-updated')
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
