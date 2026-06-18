<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <x-page-header title="Support detail" />

                <p class="mt-1 text-sm text-gray-600 sm:text-base">
                    Bekijk de details van deze supportaanvraag en voeg een antwoord toe.
                </p>
            </div>

            <a
                href="{{ route('tickets.warehouse.index') }}"
                class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
            >
                ← Terug naar support
            </a>
        </div>

        <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-5 flex flex-col gap-3 border-b border-gray-100 pb-5 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-gray-400">
                        Supportaanvraag
                    </p>

                    <h2 class="mt-1 text-2xl font-bold text-[#0F4C81]">
                        {{ $ticket->subject }}
                    </h2>

                    <p class="mt-2 text-sm text-gray-500">
                        Aangemaakt op {{ $ticket->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>

                <div class="shrink-0">
                    <x-status-badge :status="$ticket->status" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm font-semibold text-gray-500">
                        Technieker
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        {{ $ticket->user?->name ?? 'Onbekend' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-500">
                        Gekoppelde bestelling
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        Bestelling #{{ $ticket->order_id }}
                    </p>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-500">
                        Depot/provincie
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        {{ $ticket->location?->province ?? 'Geen provincie ingesteld' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-500">
                        Depot
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        {{ $ticket->location?->name ?? 'Geen depot gekoppeld' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm font-semibold text-gray-500">
                        Stad
                    </p>

                    <p class="mt-1 font-semibold text-gray-900">
                        {{ $ticket->location?->city ?? 'Geen stad gekoppeld' }}
                    </p>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-[#0F4C81]">
                    Beschrijving
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Probleem dat door de technieker werd gemeld.
                </p>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4 text-gray-700">
                {{ $ticket->description }}
            </div>
        </section>

        @if($ticket->warehouse_note)
            <section class="rounded-2xl border border-blue-200 bg-blue-50 p-4 shadow-sm sm:p-6">
                <div class="mb-3">
                    <h2 class="text-xl font-bold text-blue-700">
                        Antwoord van magazijn
                    </h2>

                    <p class="mt-1 text-sm text-blue-700">
                        Dit antwoord is zichtbaar voor de technieker.
                    </p>
                </div>

                <div class="rounded-2xl border border-blue-200 bg-white p-4 text-gray-700">
                    {{ $ticket->warehouse_note }}
                </div>
            </section>
        @endif

        <form
            method="POST"
            action="{{ route('tickets.warehouse.updateStatus', $ticket) }}"
            class="space-y-6"
        >
            @csrf
            @method('PATCH')

            <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
                <div class="mb-5">
                    <h2 class="text-xl font-bold text-[#0F4C81]">
                        Ticket opvolgen
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Pas de status aan en voeg eventueel een antwoord toe voor de technieker.
                    </p>
                </div>

                <div class="space-y-5">
                    <div>
                        <label for="status" class="mb-2 block text-sm font-semibold text-gray-700">
                            Status aanpassen
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
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
                        <label for="warehouse_note" class="mb-2 block text-sm font-semibold text-gray-700">
                            Antwoord/opmerking voor technieker
                        </label>

                        <textarea
                            id="warehouse_note"
                            name="warehouse_note"
                            rows="5"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                            placeholder="Bijvoorbeeld: Het ontbrekende materiaal wordt morgen klaargelegd."
                        >{{ old('warehouse_note', $ticket->warehouse_note) }}</textarea>

                        @error('warehouse_note')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>
            </section>

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <a
                    href="{{ route('tickets.warehouse.index') }}"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
                >
                    Annuleren
                </a>

                <x-button type="submit" class="w-full justify-center sm:w-auto">
                    Ticket opslaan
                </x-button>
            </div>
        </form>
    </div>
</x-app-layout>
