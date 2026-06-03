<x-app-layout>

    <h1>Mijn Tickets</h1>

    @forelse($tickets as $ticket)
        <div>
            <h2>{{$ticket->subject}}</h2>
            <p>{{$ticket->status}}</p>
            <p>{{$ticket->description}}</p>
            <p>Aangemaakt op: {{$ticket->created_at->format('d/m/Y')}}</p>
        </div>
    @empty
        <p>je hebt nog geen tickets.</p>
    @endforelse
</x-app-layout>


