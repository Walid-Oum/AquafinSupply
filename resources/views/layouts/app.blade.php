<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Aquafin Supply</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.1/dist/cdn.min.js"></script>
</head>

<body class="bg-[#F5F8FC]">

<div class="flex min-h-screen">

    @include('components.sidebar')

    <div class="flex-1 flex flex-col">

        @include('components.navbar')

        <main class="p-8">

            {{ $slot }}

        </main>

    </div>

</div>

</body>
</html>