{{-- 
    Pagina: Winkelmandje

    User Stories:
    US9 - Materiaal bestellen
    US10 - Leverdatum kiezen
    US11 - Meerdere materialen bestellen
--}}

<x-app-layout>

    <x-page-header title="Winkelmandje"/>

    <x-card>

        @if(session()->has('cart') && count(session()->get('cart')) > 0)

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

                <tbody>

                    @foreach(session()->get('cart') as $id => $item)

                        <tr class="border-b">

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

    <form action="{{ route('cart.update', $id) }}"
          method="POST"
          class="flex items-center gap-2">

        @csrf
        @method('PATCH')

       <input
    type="number"
    name="quantity"
    value="{{ $item['quantity'] }}"
    min="1"
    max="{{ $item['stock'] }}"
    onchange="this.form.submit()"
    class="border rounded-lg px-3 py-2 w-20">

       

    </form>

</td>

                            <td class="p-3">

                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">

                                        Verwijderen

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        @else

            <p class="text-gray-600 text-center py-8 text-lg">
                Je winkelmandje is leeg.
            </p>

        @endif

    </x-card>

    @if(session()->has('cart') && count(session()->get('cart')) > 0)

        <div class="mt-6">

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
    class="w-full border rounded-lg px-4 py-3">

            </div>

            <div>

                <label class="block mb-2 font-semibold">
                    Opmerking
                </label>

               <textarea
    name="comment"
    rows="4"
    class="w-full border rounded-lg px-4 py-3"
    placeholder="Extra informatie...">{{ old('comment') }}</textarea>

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

    @endif

</x-app-layout>