{{--
    Pagina: Detail materiaal

    User Stories:
    US8 - Materiaal detail bekijken
    US9 - Materiaal bestellen
--}}

@php
    $localStock = $material->stocks->first();

    $stock = $localStock?->stock ?? 0;
    $minimumStock = $localStock?->minimum_stock ?? $material->minimum_stock ?? 0;

    $categoryImages = [
        'Aquafin tools' => 'aquafintools.png',
        'Bevestigingsmateriaal' => 'bevestigingsmateriaal.png',
        'Gereedschap' => 'gereedschap.png',
        'PBM' => 'PBM.png',
        'Technisch onderhoud' => 'technischeonderhoud.png',
        'Verbruiksgoederen' => 'verbruiksgoederen.png',
    ];

    $fallbackImage = $categoryImages[$material->category] ?? 'sidebar-bg.jpg';

    $imageUrl = $material->image
        ? asset('storage/' . $material->image)
        : asset('images/' . $fallbackImage);

    if (! $material->is_active) {
        $stockStatusText = 'Niet actief';
        $stockStatusClasses = 'bg-gray-100 text-gray-700';
        $stockColorClass = 'text-gray-600';
    } elseif ($stock <= 0) {
        $stockStatusText = 'Geen voorraad';
        $stockStatusClasses = 'bg-red-100 text-red-700';
        $stockColorClass = 'text-red-600';
    } elseif ($stock <= $minimumStock) {
        $stockStatusText = 'Lage voorraad';
        $stockStatusClasses = 'bg-orange-100 text-orange-700';
        $stockColorClass = 'text-orange-600';
    } else {
        $stockStatusText = 'Beschikbaar';
        $stockStatusClasses = 'bg-green-100 text-green-700';
        $stockColorClass = 'text-green-600';
    }

    $canOrder = $material->is_active && $stock > 0;
@endphp

<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <x-page-header title="Materiaal detail" />

                <p class="mt-1 text-sm text-gray-600 sm:text-base">
                    Bekijk de details van dit materiaal en voeg het toe aan je winkelmandje.
                </p>
            </div>

            <a
                href="{{ route('technician.materials.index') }}"
                class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
            >
                ← Terug naar materialen
            </a>
        </div>

        <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
            <div class="mb-5 flex flex-col gap-3 border-b border-gray-100 pb-5 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-gray-400">
                        {{ $material->category }}
                    </p>

                    <h2 class="mt-1 text-2xl font-bold text-[#0F4C81]">
                        {{ $material->name }}
                    </h2>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-sm font-semibold {{ $stockStatusClasses }}">
                        {{ $stockStatusText }}
                    </span>

                    @if($material->is_active)
                        <span class="inline-flex w-fit rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-700">
                            Actief
                        </span>
                    @else
                        <span class="inline-flex w-fit rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-700">
                            Inactief
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(260px,420px)_1fr] lg:items-start">
                {{-- Afbeelding --}}
                <div class="rounded-2xl bg-gray-50 p-4">
                    <div class="flex min-h-56 items-center justify-center rounded-xl bg-white p-4 sm:min-h-72">
                        <img
                            src="{{ $imageUrl }}"
                            class="max-h-72 max-w-full object-contain"
                            alt="{{ $material->name }}"
                        >
                    </div>
                </div>

                {{-- Info --}}
                <div class="space-y-5">
                    <div>
                        <p class="text-sm font-semibold text-gray-500">
                            Beschrijving
                        </p>

                        <p class="mt-1 leading-relaxed text-gray-900">
                            {{ $material->description ?? 'Geen beschrijving beschikbaar.' }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                        <p class="mb-4 text-sm font-semibold text-gray-700">
                            Voorraadgegevens
                        </p>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Voorraad
                                </p>

                                <p class="mt-1 text-2xl font-bold {{ $stockColorClass }}">
                                    {{ $stock }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Minimum
                                </p>

                                <p class="mt-1 text-2xl font-bold text-gray-900">
                                    {{ $minimumStock }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 border-t border-gray-200 pt-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                Status
                            </p>

                            <span class="mt-2 inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $stockStatusClasses }}">
                                {{ $stockStatusText }}
                            </span>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                        @if($canOrder)
                            <form
                                action="{{ route('cart.add', $material->id) }}"
                                method="POST"
                                class="js-add-to-cart-form"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-xl bg-[#0F4C81] px-5 py-3 font-semibold text-white transition hover:bg-[#0d416f]"
                                >
                                    🛒 Toevoegen aan winkelmandje
                                </button>
                            </form>

                            <p
                                class="js-add-to-cart-message mt-3 hidden rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700"
                            >
                                Materiaal toegevoegd aan winkelmandje.
                            </p>
                        @else
                            <button
                                type="button"
                                disabled
                                class="inline-flex w-full cursor-not-allowed items-center justify-center rounded-xl bg-gray-200 px-5 py-3 font-semibold text-gray-500"
                            >
                                Niet beschikbaar
                            </button>

                            <p class="mt-3 text-sm text-gray-500">
                                Dit materiaal kan momenteel niet besteld worden.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
            <a
                href="{{ route('technician.materials.index') }}"
                class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
            >
                Terug
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('.js-add-to-cart-form');

            if (! form) {
                return;
            }

            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                const button = form.querySelector('button[type="submit"]');
                const message = document.querySelector('.js-add-to-cart-message');

                button.disabled = true;
                button.textContent = 'Toevoegen...';

                try {
                    const formData = new FormData(form);

                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    });

                    const data = await response.json();

                    if (! response.ok || ! data.success) {
                        throw new Error(data.message || 'Er ging iets mis.');
                    }

                    const cartCountElement = document.getElementById('cart-count');

                    if (cartCountElement && typeof data.cart_count !== 'undefined') {
                        cartCountElement.textContent = data.cart_count;

                        if (data.cart_count > 0) {
                            cartCountElement.classList.remove('hidden');
                        }
                    }

                    if (message) {
                        message.classList.remove('hidden');

                        setTimeout(function () {
                            message.classList.add('hidden');
                        }, 2500);
                    }
                } catch (error) {
                    alert(error.message);
                } finally {
                    button.disabled = false;
                    button.textContent = '🛒 Toevoegen aan winkelmandje';
                }
            });
        });
    </script>
</x-app-layout>
