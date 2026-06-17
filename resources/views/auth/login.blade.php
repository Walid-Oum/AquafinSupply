<x-guest-layout>

<div class="bg-white rounded-3xl shadow-2xl p-10">

    <div class="text-center mb-10">

        <h1 class="text-5xl font-extrabold text-[#0F4C81]">
            Aquafin
        </h1>

        <p class="text-gray-500 mt-2">
            Supply App
        </p>

    </div>

    <x-breeze.auth-session-status
        class="mb-4"
        :status="session('status')" />
        @if(session('success'))
    <div class="mb-4 rounded-lg bg-green-100 border border-green-300 text-green-800 px-4 py-3">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3">
        {{ $errors->first() }}
    </div>
@endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label class="block mb-2 font-semibold text-gray-700">
                Email
            </label>

            <x-breeze.text-input
                id="email"
                class="w-full"
                type="email"
                name="email"
                :value="old('email')"
                required />
        </div>

        <div class="mt-5">
            <label class="block mb-2 font-semibold text-gray-700">
                Wachtwoord
            </label>

            <x-breeze.text-input
                id="password"
                class="w-full"
                type="password"
                name="password"
                required />
        </div>

        <div class="mt-5 flex items-center">

            <input
                type="checkbox"
                name="remember">

            <span class="ml-2 text-gray-600">
                Onthoud mij
            </span>

        </div>

        <button
            type="submit"
            class="w-full mt-6 bg-[#0F4C81] hover:bg-[#1E6BA8] text-white py-3 rounded-xl font-semibold">

            Inloggen

        </button>

        <div class="text-center mt-4">

            <a
                href="{{ route('password.request') }}"
                class="text-[#1E6BA8] hover:underline">

                Wachtwoord vergeten?

            </a>

        </div>

    </form>

</div>

</x-guest-layout>