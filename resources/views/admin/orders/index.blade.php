<x-app-layout>

    <h1>Alle Bestellingen</h1>

    @foreach($orders as $order)

        <p>
            #{{ $order['id'] }}
            -
            {{ $order['technieker'] }}
            -
            {{ $order['status'] }}
        </p>

    @endforeach

</x-app-layout>
