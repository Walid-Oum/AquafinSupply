<section class="border border-red-200 bg-red-50 rounded-xl p-6">

    <header class="mb-6">

        <h2 class="text-2xl font-bold text-red-700">
            Account verwijderen
        </h2>

        <p class="mt-2 text-gray-600">
            Deze actie is permanent. Alle gegevens van uw account worden definitief verwijderd.
        </p>

    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg transition">

        Account verwijderen

    </button>

    <x-breeze.modal
        name="confirm-user-deletion"
        :show="$errors->userDeletion->isNotEmpty()"
        focusable>

        <form
            method="POST"
            action="{{ route('profile.destroy') }}"
            class="p-6">

            @csrf
            @method('DELETE')

            <h2 class="text-2xl font-bold text-red-700 mb-3">
                Account verwijderen?
            </h2>

            <p class="text-gray-600 mb-6">
                Om uw account definitief te verwijderen, voer uw wachtwoord in.
            </p>

            <div>

                <label
                    for="password"
                    class="block text-sm font-medium text-gray-700 mb-2">

                    Wachtwoord

                </label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-red-400"
                    placeholder="Wachtwoord">

                <x-breeze.input-error
                    :messages="$errors->userDeletion->get('password')"
                    class="mt-2" />

            </div>

            <div class="mt-6 flex justify-end gap-3">

                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">

                    Annuleren

                </button>

                <button
                    type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">

                    Account verwijderen

                </button>

            </div>

        </form>

    </x-breeze.modal>

</section>