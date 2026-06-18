<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Aquafin Supply</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.1/dist/cdn.min.js"></script>
</head>

<body
    class="min-h-screen bg-cover bg-center font-sans antialiased"
    style="background-image: url('{{ asset('images/sidebar-bg.jpg') }}');"
>
<div class="flex min-h-screen items-center justify-center bg-black/30 px-4 py-8 sm:px-6">
    <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl sm:p-8 md:p-10">
        {{ $slot }}
    </div>
</div>
</body>

</html>
