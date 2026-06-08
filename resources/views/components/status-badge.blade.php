@props(['status'])

@php



    $color = match($status){

    'Nieuw' => 'bg-blue-100 text-blue-700',

    'In voorbereiding' => 'bg-yellow-100 text-yellow-700',

    'Klaar om af te halen' => 'bg-green-100 text-green-700',

    'Afgehaald' => 'bg-gray-100 text-gray-700',

    default => 'bg-gray-100 text-gray-700'

}; 

@endphp

<span class="px-3 py-1 rounded-full text-sm {{ $color }}">

    {{ $status }}

</span>