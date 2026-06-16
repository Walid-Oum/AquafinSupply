<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Aquafin Supply</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.1/dist/cdn.min.js"></script>
</head>

<body
    class="min-h-screen bg-cover bg-center"
    style="background-image: url('{{ asset('images/sidebar-bg.jpg') }}');">

    <div class="min-h-screen flex items-center justify-center bg-black/30">

        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">

            {{ $slot }}

        </div>

    </div>

</body>
</html>
