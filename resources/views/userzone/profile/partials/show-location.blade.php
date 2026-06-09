<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Locatie') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Deze locatie wordt gebruikt voor het overstromingsrisico.') }}
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
