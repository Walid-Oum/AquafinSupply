{{--
    Pagina: Winkelmandje

    User Stories:
    US9 - Materiaal bestellen
    US10 - Leverdatum kiezen
    US11 - Meerdere materialen bestellen
--}}

@php
    $cart = session()->get('cart', []);
    $cartCount = count($cart);
@endphp

<x-app-layout>
    <div class="space-y-6">
        <div>
            <x-page-header title="Winkelmandje" />

            <p class="mt-1 text-sm text-gray-600 sm:text-base">
                Controleer je materialen, pas aantallen aan en plaats je bestelling.
            </p>
        </div>

        <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-4 py-4 sm:px-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-[#0F4C81]">
                            Materialen in je winkelmandje
                        </h2>

                        <p
                            id="cart-summary-text"
                            class="text-sm text-gray-500"
                        >
                            {{ $cartCount }} product(en) in je winkelmandje.
                        </p>
                    </div>

                    <div
                        id="cart-clear-wrapper"
                        class="{{ $cartCount > 0 ? '' : 'hidden' }}"
                    >
                        <form
                            action="{{ route('cart.clear') }}"
                            method="POST"
                            class="js-cart-clear-form"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center rounded-xl bg-red-100 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-200 sm:w-auto"
                            >
                                Winkelmandje leegmaken
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div
                id="cart-content"
                class="{{ $cartCount > 0 ? '' : 'hidden' }}"
            >
                {{-- Mobile card layout --}}
                <div class="space-y-3 p-4 md:hidden">
                    @foreach($cart as $id => $item)
                        <article
                            class="js-cart-row rounded-2xl border border-gray-100 bg-gray-50 p-4 shadow-sm"
                            id="cart-card-{{ $id }}"
                            data-cart-row="{{ $id }}"
                        >
                            <div class="mb-3 flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="font-bold leading-snug text-gray-900">
                                        {{ $item['name'] }}
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $item['category'] }}
                                    </p>
                                </div>

                                <form
                                    action="{{ route('cart.remove', $id) }}"
                                    method="POST"
                                    class="js-cart-remove-form shrink-0"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600 transition hover:bg-red-100 hover:text-red-800"
                                        title="Verwijderen"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.8"
                                            stroke="currentColor"
                                            class="h-5 w-5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M6 7.5h12m-10.5 0v10.125A1.875 1.875 0 009.375 19.5h5.25A1.875 1.875 0 0016.5 17.625V7.5m-6 0V5.625A1.125 1.125 0 0111.625 4.5h.75A1.125 1.125 0 0113.5 5.625V7.5"
                                            />
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-xl bg-white p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Voorraad
                                    </p>

                                    <p class="mt-1 text-lg font-bold text-gray-800">
                                        {{ $item['stock'] }}
                                    </p>
                                </div>

                                <div class="rounded-xl bg-white p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                        Aantal
                                    </p>

                                    <form
                                        action="{{ route('cart.update', $id) }}"
                                        method="POST"
                                        class="js-cart-update-form mt-1 flex items-center gap-2"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="number"
                                            name="quantity"
                                            value="{{ $item['quantity'] }}"
                                            min="1"
                                            max="{{ $item['stock'] }}"
                                            data-original-quantity="{{ $item['quantity'] }}"
                                            class="js-cart-quantity w-full rounded-lg border border-gray-200 px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
                                        >

                                        <span class="js-cart-status text-sm text-gray-500"></span>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Desktop table layout --}}
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full min-w-[760px]">
                        <thead>
                        <tr class="border-b bg-gray-50 text-sm text-gray-600">
                            <th class="p-4 text-left">
                                Materiaal
                            </th>

                            <th class="p-4 text-left">
                                Categorie
                            </th>

                            <th class="p-4 text-left">
                                Voorraad
                            </th>

                            <th class="p-4 text-left">
                                Aantal
                            </th>

                            <th class="p-4 text-left">
                                Actie
                            </th>
                        </tr>
                        </thead>

                        <tbody id="cart-table-body">
                        @foreach($cart as $id => $item)
                            <tr
                                class="js-cart-row border-b border-gray-100 last:border-0"
                                id="cart-row-{{ $id }}"
                                data-cart-row="{{ $id }}"
                            >
                                <td class="p-4 font-medium text-gray-900">
                                    {{ $item['name'] }}
                                </td>

                                <td class="p-4 text-gray-700">
                                    {{ $item['category'] }}
                                </td>

                                <td class="p-4 text-gray-700">
                                    {{ $item['stock'] }}
                                </td>

                                <td class="p-4">
                                    <form
                                        action="{{ route('cart.update', $id) }}"
                                        method="POST"
                                        class="js-cart-update-form flex items-center gap-2"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="number"
                                            name="quantity"
                                            value="{{ $item['quantity'] }}"
                                            min="1"
                                            max="{{ $item['stock'] }}"
                                            data-original-quantity="{{ $item['quantity'] }}"
                                            class="js-cart-quantity w-20 rounded-lg border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
                                        >

                                        <span class="js-cart-status text-sm text-gray-500"></span>
                                    </form>
                                </td>

                                <td class="p-4">
                                    <form
                                        action="{{ route('cart.remove', $id) }}"
                                        method="POST"
                                        class="js-cart-remove-form"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="text-red-600 transition hover:text-red-800"
                                            title="Verwijderen"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.8"
                                                stroke="currentColor"
                                                class="h-6 w-6"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M6 7.5h12m-10.5 0v10.125A1.875 1.875 0 009.375 19.5h5.25A1.875 1.875 0 0016.5 17.625V7.5m-6 0V5.625A1.125 1.125 0 0111.625 4.5h.75A1.125 1.125 0 0113.5 5.625V7.5"
                                                />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <p
                id="cart-empty-message"
                class="{{ $cartCount > 0 ? 'hidden' : '' }} px-4 py-10 text-center text-lg text-gray-600"
            >
                Je winkelmandje is leeg.
            </p>
        </section>

        <section
            id="order-form-wrapper"
            class="{{ $cartCount > 0 ? '' : 'hidden' }}"
        >
            <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                <div class="mb-5">
                    <h2 class="text-lg font-bold text-[#0F4C81]">
                        Bestelling afronden
                    </h2>

                    <p class="text-sm text-gray-500">
                        Kies wanneer je de materialen nodig hebt.
                    </p>
                </div>

                <form action="{{ route('orders.store') }}" method="POST">
                    @csrf

                    <div class="space-y-5">
                        <div>
                            <label for="delivery_date" class="mb-2 block font-semibold text-gray-700">
                                Leverdatum
                            </label>

                            <input
                                id="delivery_date"
                                type="date"
                                name="delivery_date"
                                value="{{ old('delivery_date') }}"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
                            >

                            @error('delivery_date')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label for="comment" class="mb-2 block font-semibold text-gray-700">
                                Opmerking
                            </label>

                            <textarea
                                id="comment"
                                name="comment"
                                rows="4"
                                class="w-full rounded-xl border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
                                placeholder="Extra informatie..."
                            >{{ old('comment') }}</textarea>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                            <a
                                href="{{ route('technician.materials.index') }}"
                                class="inline-flex w-full items-center justify-center rounded-xl bg-gray-100 px-5 py-3 font-semibold text-gray-700 transition hover:bg-gray-200 sm:w-auto"
                            >
                                Verder winkelen
                            </a>

                            <x-button type="submit" class="w-full justify-center sm:w-auto">
                                Bestelling plaatsen
                            </x-button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function updateCartCount(count) {
                const cartCountElement = document.getElementById('cart-count');

                if (! cartCountElement) {
                    return;
                }

                cartCountElement.textContent = count;

                if (count > 0) {
                    cartCountElement.classList.remove('hidden');
                } else {
                    cartCountElement.classList.add('hidden');
                }
            }

            function updateCartSummary(count) {
                const cartClearWrapper = document.getElementById('cart-clear-wrapper');
                const summaryText = document.getElementById('cart-summary-text');

                if (summaryText) {
                    summaryText.textContent = `${count} product(en) in je winkelmandje.`;
                }

                if (cartClearWrapper && count <= 0) {
                    cartClearWrapper.classList.add('hidden');
                }
            }

            function updateEmptyState(isEmpty) {
                const cartContent = document.getElementById('cart-content');
                const emptyMessage = document.getElementById('cart-empty-message');
                const orderFormWrapper = document.getElementById('order-form-wrapper');

                if (isEmpty) {
                    cartContent.classList.add('hidden');
                    emptyMessage.classList.remove('hidden');
                    orderFormWrapper.classList.add('hidden');
                }
            }

            function syncDuplicateQuantities(rowId, quantity) {
                document.querySelectorAll(`[data-cart-row="${rowId}"] .js-cart-quantity`).forEach(function (input) {
                    input.value = quantity;
                    input.dataset.originalQuantity = quantity;
                });
            }

            async function sendCartRequest(form) {
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

                return data;
            }

            document.querySelectorAll('.js-cart-update-form').forEach(function (form) {
                const input = form.querySelector('.js-cart-quantity');
                const status = form.querySelector('.js-cart-status');

                input.addEventListener('change', async function () {
                    const originalQuantity = input.dataset.originalQuantity;
                    const row = form.closest('.js-cart-row');
                    const rowId = row ? row.dataset.cartRow : null;

                    input.readOnly = true;
                    status.textContent = '';

                    try {
                        const data = await sendCartRequest(form);

                        input.dataset.originalQuantity = input.value;

                        if (rowId) {
                            syncDuplicateQuantities(rowId, input.value);
                        }

                        updateCartCount(data.cart_count);
                        updateCartSummary(data.cart_count);
                        status.textContent = '';
                    } catch (error) {
                        input.value = originalQuantity;
                        status.textContent = '';
                        alert(error.message);
                    } finally {
                        input.readOnly = false;
                    }
                });
            });

            document.querySelectorAll('.js-cart-remove-form').forEach(function (form) {
                form.addEventListener('submit', async function (event) {
                    event.preventDefault();

                    const button = form.querySelector('button[type="submit"]');
                    const row = form.closest('.js-cart-row');
                    const rowId = row ? row.dataset.cartRow : null;

                    button.disabled = true;

                    try {
                        const data = await sendCartRequest(form);

                        if (rowId) {
                            document.querySelectorAll(`[data-cart-row="${rowId}"]`).forEach(function (matchingRow) {
                                matchingRow.remove();
                            });
                        } else if (row) {
                            row.remove();
                        }

                        updateCartCount(data.cart_count);
                        updateCartSummary(data.cart_count);
                        updateEmptyState(data.cart_empty);
                    } catch (error) {
                        button.disabled = false;
                        alert(error.message);
                    }
                });
            });

            const clearForm = document.querySelector('.js-cart-clear-form');

            if (clearForm) {
                clearForm.addEventListener('submit', async function (event) {
                    event.preventDefault();

                    const confirmed = confirm('Ben je zeker dat je het volledige winkelmandje wilt leegmaken?');

                    if (! confirmed) {
                        return;
                    }

                    const button = clearForm.querySelector('button[type="submit"]');

                    button.disabled = true;
                    button.textContent = 'Leegmaken...';

                    try {
                        const data = await sendCartRequest(clearForm);

                        document.querySelectorAll('.js-cart-row').forEach(function (row) {
                            row.remove();
                        });

                        updateCartCount(data.cart_count);
                        updateCartSummary(data.cart_count);
                        updateEmptyState(true);
                    } catch (error) {
                        button.disabled = false;
                        button.textContent = 'Winkelmandje leegmaken';
                        alert(error.message);
                    }
                });
            }
        });
    </script>
</x-app-layout>
