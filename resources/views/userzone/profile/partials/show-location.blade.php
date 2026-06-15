<section>
    <header>
        <h2 class="text-2xl font-bold text-[#0F4C81]">
            {{ __('Locatie') }}
        </h2>

        <p class="text-gray-500 mt-2">
    Deze locatie wordt gebruikt voor uw materiaalbestellingen en overstromingsrisico.
</p>
    </header>

    <div class="mt-6">
        <x-breeze.input-label for="location" value="{{ __('Gekoppelde locatie') }}" />

        <div
            id="location"
            class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-gray-700 shadow-sm"
        >
            @if (auth()->user()->location)
                {{ auth()->user()->location->name }} - {{ auth()->user()->location->city }}
            @else
                {{ __('Geen locatie gekoppeld') }}
            @endif
        </div>

        @if(auth()->user()->role === 'admin')
            <p class="mt-2 text-sm text-gray-600">
                {{ __('Deze locatie wordt gebruikt als standaardlocatie voor het overstromingsrisico. Locaties van gebruikers kunnen aangepast worden via gebruikersbeheer.') }}
            </p>
        @else
            <p class="mt-2 text-sm text-gray-600">
                {{ __('Deze locatie kan enkel door een administrator aangepast worden.') }}
            </p>
        @endif
    </div>
</section>
