<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Aquafin Supply</title>
<link rel="icon" type="image/png" href="{{ asset('images/aquafin-logo.png') }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.1/dist/cdn.min.js"></script>
</head>

<body class="bg-[#F5F8FC]">

<div class="flex min-h-screen">

    @include('components.sidebar')

    <div class="flex-1 flex flex-col">

        @include('components.navbar')

        @if(session('success'))

    <div class="mx-6 mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">

        {{ session('success') }}

    </div>

@endif

@if(session('error'))

    <div class="mx-6 mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">

        {{ session('error') }}

    </div>

@endif
@if ($errors->any())

    <div class="mx-6 mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">

        <ul>

            @foreach ($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif
@if(Auth::check() && Auth::user()->role == 'technieker')

<div
    id="gasReminder"
    class="mx-6 mt-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded-lg shadow">

    <h2 class="font-bold text-lg mb-2">
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
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">

        ✓ Ik heb dit gecontroleerd

    </button>

</div>

<script>

document.addEventListener("DOMContentLoaded", function () {

    if(sessionStorage.getItem("gasReminder") === "done"){
        document.getElementById("gasReminder").style.display = "none";
    }

    document.getElementById("closeReminder").addEventListener("click", function(){

       sessionStorage.setItem("gasReminder", "done");

        document.getElementById("gasReminder").style.display = "none";

    });

});

</script>

@endif
        <main class="p-8">
 

            {{ $slot }}

        </main>

    </div>

</div>

</body>
</html>