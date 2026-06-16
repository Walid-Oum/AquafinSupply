@props(['status'])

@php
    $classes = match ($status) {
        'Nieuw' => 'bg-blue-100 text-blue-700',
        'In voorbereiding' => 'bg-yellow-100 text-yellow-700',
        'Klaar om af te halen' => 'bg-green-100 text-green-700',
        'Afgehaald' => 'bg-gray-100 text-gray-700',
        'Geannuleerd' => 'bg-red-100 text-red-700',

        'Open' => 'bg-red-100 text-red-700',
        'In behandeling' => 'bg-blue-100 text-blue-700',
        'Opgelost' => 'bg-green-100 text-green-700',

        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold whitespace-nowrap {{ $classes }}">
    {{ $status }}
</span>
