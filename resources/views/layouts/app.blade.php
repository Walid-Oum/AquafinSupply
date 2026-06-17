<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Aquafin Supply</title>

    <link rel="icon" type="image/png" href="{{ asset('images/aquafin-logo.png') }}">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.1/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-[#F5F8FC]">

<div
    x-data="{ mobileSidebarOpen: false }"
    @keydown.escape.window="mobileSidebarOpen = false"
    class="min-h-screen lg:flex"
>
    {{-- Desktop sidebar --}}
    <div class="hidden lg:block">
        @include('components.sidebar', ['mobile' => false])
    </div>

    {{-- Mobile overlay --}}
    <div
        x-show="mobileSidebarOpen"
        x-cloak
        x-transition.opacity
        @click="mobileSidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/50 lg:hidden"
    ></div>

    {{-- Mobile sidebar drawer --}}
    <div
        x-cloak
        class="fixed inset-y-0 left-0 z-50 w-72 transform transition-transform duration-300 ease-in-out lg:hidden"
        :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        @include('components.sidebar', ['mobile' => true])
    </div>

    <div class="flex min-h-screen flex-1 flex-col">

        @include('components.navbar')

        @if(session('success'))
            <div class="mx-4 mt-4 rounded-lg border border-green-400 bg-green-100 p-4 text-green-700 sm:mx-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mx-4 mt-4 rounded-lg border border-red-400 bg-red-100 p-4 text-red-700 sm:mx-6">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mx-4 mt-4 rounded-lg border border-red-400 bg-red-100 p-4 text-red-700 sm:mx-6">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(Auth::check() && Auth::user()->role == 'technieker')
            <div
                id="gasReminder"
                class="mx-4 mt-4 rounded-lg border-l-4 border-yellow-500 bg-yellow-100 p-4 text-yellow-800 shadow sm:mx-6"
            >
                <h2 class="mb-2 text-lg font-bold">
                    🔔 Herinnering
                </h2>

                <p>
                    Vergeet uw gastoestel niet op te laden.
                </p>

                <p class="mb-4">
                    Vergeet uw gastoestel niet mee te nemen.
                </p>

                <button
                    id="closeReminder"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    ✓ Ik heb dit gecontroleerd
                </button>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const reminder = document.getElementById("gasReminder");
                    const closeButton = document.getElementById("closeReminder");

                    if (! reminder || ! closeButton) {
                        return;
                    }

                    if (sessionStorage.getItem("gasReminder") === "done") {
                        reminder.style.display = "none";
                    }

                    closeButton.addEventListener("click", function () {
                        sessionStorage.setItem("gasReminder", "done");
                        reminder.style.display = "none";
                    });
                });
            </script>
        @endif

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>

    </div>
</div>

</body>
</html>
