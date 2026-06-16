<x-app-layout>
    <div class="p-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <x-page-header title="Support detail" />

                <p class="text-gray-600">
                    Bekijk hier de details van deze supportaanvraag.
                </p>
            </div>

            <a href="{{ route('tickets.warehouse.index') }}"
               class="rounded-lg bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">
                Terug
            </a>
        </div>

        <div class="rounded-xl bg-white p-6 shadow">
            <div class="mb-6 flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">
                        {{ $ticket->subject }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Aangemaakt op {{ $ticket->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>

                <x-status-badge :status="$ticket->status" />
            </div>

            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-sm font-medium text-gray-500">
                        Technieker
                    </p>

                    <p class="mt-1 text-gray-900">
                        {{ $ticket->user->name ?? 'Onbekend' }}
                    </p>
                </div>

                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-sm font-medium text-gray-500">
                        Gekoppelde bestelling
                    </p>

                    <p class="mt-1 text-gray-900">
                        Bestelling #{{ $ticket->order_id }}
                    </p>
                </div>

                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-sm font-medium text-gray-500">
                        Depot/provincie
                    </p>

                    <p class="mt-1 text-gray-900">
                        {{ $ticket->location->province ?? 'Geen provincie ingesteld' }}
                    </p>
                </div>

                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-sm font-medium text-gray-500">
                        Depot
                    </p>

                    <p class="mt-1 text-gray-900">
                        {{ $ticket->location->name ?? 'Geen depot gekoppeld' }}
                    </p>
                </div>

                <div class="rounded-lg bg-gray-50 p-4">
                    <p class="text-sm font-medium text-gray-500">
                        Stad
                    </p>

                    <p class="mt-1 text-gray-900">
                        {{ $ticket->location->city ?? 'Geen stad gekoppeld' }}
                    </p>
                </div>
            </div>

            <div>
                <h3 class="mb-2 font-semibold text-gray-900">
                    Beschrijving
                </h3>

                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-gray-700">
                    {{ $ticket->description }}
                </div>
            </div>

            @if($ticket->warehouse_note)
                <div class="mt-6">
                    <h3 class="mb-2 font-semibold text-gray-900">
                        Antwoord van magazijn
                    </h3>

                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-gray-700">
                        {{ $ticket->warehouse_note }}
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('tickets.warehouse.updateStatus', $ticket) }}" class="mt-6">
                @csrf
                @method('PATCH')

                <div class="space-y-4">
                    <div>
                        <label for="status" class="mb-1 block text-sm font-semibold text-gray-700">
                            Status aanpassen
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="rounded-lg border border-gray-300 px-3 py-2 shadow-sm"
                        >
                            <option value="Open" @selected($ticket->status === 'Open')>
                                Open
                            </option>

                            <option value="In behandeling" @selected($ticket->status === 'In behandeling')>
                                In behandeling
                            </option>

                            <option value="Opgelost" @selected($ticket->status === 'Opgelost')>
                                Opgelost
                            </option>
                        </select>

                        @error('status')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label for="warehouse_note" class="mb-1 block text-sm font-semibold text-gray-700">
                            Antwoord/opmerking voor technieker
                        </label>

                        <textarea
                            id="warehouse_note"
                            name="warehouse_note"
                            rows="4"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm"
                            placeholder="Bijvoorbeeld: Het ontbrekende materiaal wordt morgen klaargelegd."
                        >{{ old('warehouse_note', $ticket->warehouse_note) }}</textarea>

                        @error('warehouse_note')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    <x-button>
                        Ticket opslaan
                    </x-button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
