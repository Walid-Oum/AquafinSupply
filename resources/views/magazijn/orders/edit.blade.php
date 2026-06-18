{{--
    MAGAZIJN - BESTELLING WIJZIGEN

    @author      Chayma (Team Aquafin)
    @version     1.0
    @since       2026-06-18

    Deze view toont een formulier waarmee magazijnmedewerkers de status
    en inhoud van een bestelling kunnen aanpassen.
    Magazijnmedewerkers kunnen de status wijzigen (Nieuw, In voorbereiding,
    Klaar om af te halen, Afgehaald) en de hoeveelheden van materialen
    aanpassen of materialen uit de bestelling verwijderen.

    @see App\Http\Controllers\Userzone\OrderController::warehouseEdit()
    @see App\Http\Controllers\Userzone\OrderController::warehouseUpdate()
--}}

@php
    $categoryImages = [
        'Aquafin tools' => 'aquafintools.png',
        'Bevestigingsmateriaal' => 'bevestigingsmateriaal.png',
        'Gereedschap' => 'gereedschap.png',
        'PBM' => 'PBM.png',
        'Technisch onderhoud' => 'technischeonderhoud.png',
        'Verbruiksgoederen' => 'verbruiksgoederen.png',
    ];
@endphp

<x-app-layout>
    <div class="space-y-6">
        {{-- HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <x-page-header title="Bestelling wijzigen" />

                <p class="mt-1 text-sm text-gray-600 sm:text-base">
                    Pas de status of inhoud van deze bestelling aan.
                </p>
            </div>

            <a
                href="{{ route('magazijn.orders.show', $order->id) }}"
                class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
            >
                ← Terug naar bestelling
            </a>
        </div>

        <form
            action="{{ route('magazijn.orders.update', $order->id) }}"
            method="POST"
            class="space-y-6"
        >
            @csrf
            @method('PATCH')

            {{-- BESTELGEGEVENS --}}
            <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
                <div class="mb-5 flex flex-col gap-3 border-b border-gray-100 pb-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-gray-400">
                            Bestelling
                        </p>

                        <h2 class="mt-1 text-2xl font-bold text-[#0F4C81]">
                            #{{ $order->id }}
                        </h2>
                    </div>

                    <x-status-badge :status="$order->status" />
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Technieker
                        </label>

                        <input
                            type="text"
                            value="{{ $order->user?->name ?? 'Onbekend' }}"
                            disabled
                            class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-700"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Leverdatum
                        </label>

                        <input
                            type="text"
                            value="{{ $order->delivery_date ?? 'Geen leverdatum' }}"
                            disabled
                            class="w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-700"
                        >
                    </div>

                    {{-- STATUS DROPDOWN --}}
                    <div class="md:col-span-2">
                        <label for="status" class="mb-2 block text-sm font-semibold text-gray-700">
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                        >
                            <option
                                value="Nieuw"
                                {{ $order->status == 'Nieuw' ? 'selected' : '' }}
                            >
                                Nieuw
                            </option>

                            <option
                                value="In voorbereiding"
                                {{ $order->status == 'In voorbereiding' ? 'selected' : '' }}
                            >
                                In voorbereiding
                            </option>

                            <option
                                value="Klaar om af te halen"
                                {{ $order->status == 'Klaar om af te halen' ? 'selected' : '' }}
                            >
                                Klaar om af te halen
                            </option>

                            <option
                                value="Afgehaald"
                                {{ $order->status == 'Afgehaald' ? 'selected' : '' }}
                            >
                                Afgehaald
                            </option>
                        </select>
                    </div>
                </div>
            </section>

            {{-- MATERIALEN --}}
            <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
                    <h2 class="text-xl font-bold text-[#0F4C81]">
                        Materialen
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Pas de hoeveelheden aan. Zet een hoeveelheid op 0 om het materiaal uit de bestelling te verwijderen.
                    </p>
                </div>

                @if($order->items->count() > 0)
                    <div class="space-y-3 p-4 sm:p-6">
                        @foreach($order->items as $item)
                            @php
                                $material = $item->material;
                                $category = $material?->category ?? 'Onbekende categorie';
                                $fallbackImage = $categoryImages[$category] ?? 'sidebar-bg.jpg';

                                $imageUrl = $material?->image
                                    ? asset('storage/' . $material->image)
                                    : asset('images/' . $fallbackImage);
                            @endphp

                            <article class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex min-w-0 gap-4">
                                        <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-white p-2">
                                            <img
                                                src="{{ $imageUrl }}"
                                                alt="{{ $material?->name ?? 'Materiaal' }}"
                                                class="max-h-full max-w-full object-contain"
                                            >
                                        </div>

                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                                {{ $category }}
                                            </p>

                                            <h3 class="mt-1 font-bold leading-snug text-gray-900">
                                                {{ $material?->name ?? 'Onbekend materiaal' }}
                                            </h3>

                                            <p class="mt-1 text-sm text-gray-500">
                                                Huidige aantal: {{ $item->quantity }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Hoeveelheid aanpassen --}}
                                    <div class="sm:w-36">
                                        <label
                                            for="quantity-{{ $item->id }}"
                                            class="mb-2 block text-sm font-semibold text-gray-700 sm:text-center"
                                        >
                                            Nieuw aantal
                                        </label>

                                        <input
                                            id="quantity-{{ $item->id }}"
                                            type="number"
                                            min="0"
                                            name="quantities[{{ $item->id }}]"
                                            value="{{ $item->quantity }}"
                                            class="w-full rounded-xl border border-gray-200 px-4 py-3 text-center text-sm font-semibold shadow-sm focus:border-[#0F4C81] focus:outline-none focus:ring-2 focus:ring-[#0F4C81]/20"
                                        >
                                    </div>
                                </div>

                                <p class="mt-3 rounded-xl bg-white px-3 py-2 text-xs text-gray-500">
                                    Zet op 0 om dit materiaal uit de bestelling te verwijderen.
                                </p>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="px-4 py-10 text-center text-gray-500 italic">
                        Geen materialen gevonden.
                    </div>
                @endif
            </section>

            {{-- WAARSCHUWING --}}
            <section class="rounded-2xl border border-yellow-200 bg-yellow-50 p-4 shadow-sm sm:p-5">
                <div class="flex gap-3">
                    <div class="text-xl">
                        ⚠️
                    </div>

                    <div>
                        <p class="font-bold text-yellow-800">
                            Waarschuwing
                        </p>

                        <p class="mt-1 text-sm leading-relaxed text-yellow-800">
                            Het wijzigen van een bestelling kan gevolgen hebben voor de voorraad.
                            Controleer daarom goed de hoeveelheden voordat je opslaat.
                        </p>
                    </div>
                </div>
            </section>

            {{-- BEVESTIGING --}}
            <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                <label class="flex items-start gap-3">
                    <input
                        type="checkbox"
                        name="confirmation"
                        required
                        class="mt-1 h-5 w-5 rounded border-gray-300 text-[#0F4C81] focus:ring-[#0F4C81]"
                    >

                    <span class="text-sm font-semibold text-gray-700 sm:text-base">
                        Ik bevestig dat ik deze bestelling wil wijzigen.
                    </span>
                </label>
            </section>

            {{-- ACTIES --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <a
                    href="{{ route('magazijn.orders.show', $order->id) }}"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
                >
                    Annuleren
                </a>

                <x-button type="submit" class="w-full justify-center sm:w-auto">
                    Wijzigingen opslaan
                </x-button>
            </div>
        </form>
    </div>
</x-app-layout>