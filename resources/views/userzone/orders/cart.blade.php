{{--
    Pagina: Winkelmandje

    User Stories:
    US9 - Materiaal bestellen
    US10 - Leverdatum kiezen
    US11 - Meerdere materialen bestellen
--}}

@php
    $cart = session()->get('cart', []);
    $cartCount = collect($cart)->sum('quantity');
@endphp

<x-app-layout>

    <x-page-header title="Winkelmandje"/>

    <x-card>
        <div
            id="cart-content"
            class="{{ $cartCount > 0 ? '' : 'hidden' }}"
        >
            <div class="mb-4 flex justify-end">
                <form
                    action="{{ route('cart.clear') }}"
                    method="POST"
                    class="js-cart-clear-form"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg font-semibold"
                    >
                        Winkelmandje leegmaken
                    </button>
                </form>
            </div>

            <table class="w-full">
                <thead>
                <tr class="border-b">
                    <th class="text-left p-3">Materiaal</th>
                    <th class="text-left p-3">Categorie</th>
                    <th class="text-left p-3">Voorraad</th>
                    <th class="text-left p-3">Aantal</th>
                    <th class="text-left p-3">Actie</th>
                </tr>
                </thead>

                <tbody id="cart-table-body">
                @foreach($cart as $id => $item)
                    <tr
                        class="border-b js-cart-row"
                        id="cart-row-{{ $id }}"
                        data-cart-row="{{ $id }}"
                    >
                        <td class="p-3">
                            {{ $item['name'] }}
                        </td>

                        <td class="p-3">
                            {{ $item['category'] }}
                        </td>

                        <td class="p-3">
                            {{ $item['stock'] }}
                        </td>

                        <td class="p-3">
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
                                    class="js-cart-quantity border rounded-lg px-3 py-2 w-20"
                                >

                                <span class="js-cart-status text-sm text-gray-500"></span>
                            </form>
                        </td>

                        <td class="p-3">
                            <form
                                action="{{ route('cart.remove', $id) }}"
                                method="POST"
                                class="js-cart-remove-form"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center gap-2"
                                >
                                    Verwijderen
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <p
            id="cart-empty-message"
            class="{{ $cartCount > 0 ? 'hidden' : '' }} text-gray-600 text-center py-8 text-lg"
        >
            Je winkelmandje is leeg.
        </p>
    </x-card>

    <div
        id="order-form-wrapper"
        class="mt-6 {{ $cartCount > 0 ? '' : 'hidden' }}"
    >
        <x-card>
            <form action="{{ route('orders.store') }}" method="POST">
                @csrf

                <div class="space-y-5">
                    <div>
                        <label class="block mb-2 font-semibold">
                            Leverdatum
                        </label>

                        <input
                            type="date"
                            name="delivery_date"
                            value="{{ old('delivery_date') }}"
                            class="w-full border rounded-lg px-4 py-3"
                        >
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">
                            Opmerking
                        </label>

                        <textarea
                            name="comment"
                            rows="4"
                            class="w-full border rounded-lg px-4 py-3"
                            placeholder="Extra informatie..."
                        >{{ old('comment') }}</textarea>
                    </div>

                    <div>
                        <x-button>
                            🛒 Bestelling plaatsen
                        </x-button>
                    </div>
                </div>
            </form>
        </x-card>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function updateCartCount(count) {
                const cartCountElement = document.getElementById('cart-count');

                if (!cartCountElement) {
                    return;
                }

                cartCountElement.textContent = count;

                if (count > 0) {
                    cartCountElement.classList.remove('hidden');
                } else {
                    cartCountElement.classList.add('hidden');
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

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Er ging iets mis.');
                }

                return data;
            }

            document.querySelectorAll('.js-cart-update-form').forEach(function (form) {
                const input = form.querySelector('.js-cart-quantity');
                const status = form.querySelector('.js-cart-status');

                input.addEventListener('change', async function () {
                    const originalQuantity = input.dataset.originalQuantity;

                    input.readOnly = true;
                    status.textContent = 'Opslaan...';

                    try {
                        const data = await sendCartRequest(form);

                        input.dataset.originalQuantity = input.value;
                        status.textContent = 'Opgeslagen ✓';

                        updateCartCount(data.cart_count);

                        setTimeout(function () {
                            status.textContent = '';
                        }, 900);

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

                    button.disabled = true;
                    button.textContent = 'Verwijderen...';

                    try {
                        const data = await sendCartRequest(form);

                        row.remove();

                        updateCartCount(data.cart_count);
                        updateEmptyState(data.cart_empty);

                    } catch (error) {
                        button.disabled = false;
                        button.textContent = 'Verwijderen';
                        alert(error.message);
                    }
                });
            });

            const clearForm = document.querySelector('.js-cart-clear-form');

            if (clearForm) {
                clearForm.addEventListener('submit', async function (event) {
                    event.preventDefault();

                    const confirmed = confirm('Ben je zeker dat je het volledige winkelmandje wilt leegmaken?');

                    if (!confirmed) {
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
