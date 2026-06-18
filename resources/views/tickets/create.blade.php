{{--
    TECHNIEKER - NIEUWE SUPPORTAANVRAAG (TICKET)

    @author
    @version     1.0
    @since       2026-06-18

    Deze view toont een formulier waarmee een technieker een nieuwe
    supportaanvraag (ticket) kan aanmaken.
    De technieker kiest een bestelling, vult een onderwerp en
    beschrijving in. Het ticket wordt dan doorgestuurd naar het magazijn.

    @see App\Http\Controllers\TicketController::create()
    @see App\Http\Controllers\TicketController::store()
--}}

<x-app-layout>
    <div class="min-w-0 max-w-full space-y-6 overflow-x-hidden">
        {{-- HEADER --}}
        <div class="mx-auto w-full max-w-3xl">
            <x-page-header title="Nieuwe supportaanvraag" />

            <p class="mt-1 text-sm text-gray-600 sm:text-base">
                Meld een probleem over een bestelling zodat het magazijn dit kan opvolgen.
            </p>
        </div>

        {{-- FORMULIER --}}
        <section class="mx-auto w-full max-w-3xl rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-6 rounded-2xl bg-blue-50 p-4">
                <h2 class="font-bold text-[#0F4C81]">
                    Supportaanvraag aanmaken
                </h2>

                <p class="mt-1 text-sm leading-relaxed text-gray-600">
                    Kies de bestelling waarover je een probleem wilt melden en beschrijf kort wat er mis is.
                </p>
            </div>

            <form method="POST" action="{{ route('tickets.store') }}" class="space-y-5">
                @csrf

                {{-- BESTELLING DROPDOWN --}}
                <div>
                    <label for="order_id" class="mb-2 block text-sm font-semibold text-gray-700">
                        Bestelling
                    </label>

                    <select
                        id="order_id"
                        name="order_id"
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                    >
                        <option value="">
                            Kies een bestelling
                        </option>

                        @foreach ($orders as $order)
                            <option value="{{ $order->id }}" @selected(old('order_id') == $order->id)>
                                Bestelling #{{ $order->id }} — levering {{ $order->delivery_date ?? 'geen leverdatum' }}
                            </option>
                        @endforeach
                    </select>

                    @error('order_id')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- ONDERWERP --}}
                <div>
                    <label for="subject" class="mb-2 block text-sm font-semibold text-gray-700">
                        Onderwerp
                    </label>

                    <input
                        id="subject"
                        name="subject"
                        type="text"
                        value="{{ old('subject') }}"
                        placeholder="Bijvoorbeeld: materiaal ontbreekt"
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                    >

                    @error('subject')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- BESCHRIJVING --}}
                <div>
                    <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">
                        Beschrijving
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        placeholder="Beschrijf kort wat het probleem is."
                        class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                    >{{ old('description') }}</textarea>

                    @error('description')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- ACTIES --}}
                <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('tickets.index') }}"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
                    >
                        Annuleren
                    </a>

                    <x-button type="submit" class="w-full justify-center sm:w-auto">
                        Supportaanvraag aanmaken
                    </x-button>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
