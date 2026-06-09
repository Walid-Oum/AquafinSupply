<x-app-layout>
    <div class="p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Overstromingsrisico per regio
            </h1>

            <p class="text-gray-600">
                Kies een locatie om het voorspelde overstromingsrisico te bekijken.
            </p>
        </div>

        <div class="mb-6 rounded-xl bg-white p-6 shadow">
            <form method="GET" action="{{ route('admin.flood-risk.index') }}">
                <label for="location_id" class="mb-2 block text-sm font-semibold text-gray-700">
                    Kies een locatie
                </label>

                <div class="flex gap-3">
                    <select
                        id="location_id"
                        name="location_id"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm"
                    >
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}"
                                @selected($selectedLocation && $selectedLocation->id === $location->id)>
                                {{ $location->name }} - {{ $location->city }}
                            </option>
                        @endforeach
                    </select>

                    <button
                        type="submit"
                        class="rounded-lg bg-[#0F4C81] px-4 py-2 font-semibold text-white hover:bg-[#1E6BA8]"
                    >
                        Tonen
                    </button>
                </div>
            </form>
        </div>

        @if ($error)
            <div class="mb-6 rounded-lg bg-red-100 p-4 text-red-700">
                {{ $error }}
            </div>
        @endif

        @if ($selectedLocation)
            <div class="mb-6 rounded-xl bg-white p-6 shadow">
                <h2 class="text-xl font-semibold text-gray-900">
                    Geselecteerde locatie: {{ $selectedLocation->name }}
                </h2>

                <p class="mt-1 text-gray-600">
                    Stad: {{ $selectedLocation->city }}
                </p>

                <p class="mt-1 text-gray-600">
                    Coördinaten: {{ $selectedLocation->latitude }}, {{ $selectedLocation->longitude }}
                </p>
            </div>
        @endif

        @if ($riskLevel)
            <div class="mb-6 rounded-xl bg-white p-6 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">
                            Risico voor volgende week
                        </h2>

                        <p class="mt-2 text-gray-700">
                            Verwachte neerslag volgende week:
                            <strong>{{ $nextWeekRain }} mm</strong>
                        </p>

                        <p class="mt-1 text-gray-700">
                            Verwachte neerslag deze week:
                            <strong>{{ $currentWeekRain }} mm</strong>
                        </p>
                    </div>

                    @if($riskLevel === 'Hoog')
                        <span class="rounded-full bg-red-100 px-4 py-2 text-sm font-semibold text-red-700">
                            Hoog risico
                        </span>
                    @elseif($riskLevel === 'Gemiddeld')
                        <span class="rounded-full bg-yellow-100 px-4 py-2 text-sm font-semibold text-yellow-700">
                            Gemiddeld risico
                        </span>
                    @else
                        <span class="rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-700">
                            Laag risico
                        </span>
                    @endif
                </div>
            </div>

            <div class="mb-6 rounded-xl bg-white p-6 shadow">
                <h2 class="text-xl font-semibold text-gray-900">
                    Administratief advies
                </h2>

                @if($riskLevel === 'Hoog')
                    <p class="mt-2 text-gray-700">
                        Deze regio heeft een hoog risico. Controleer of er voldoende overstromingsmateriaal beschikbaar is en volg bestellingen uit deze regio extra goed op.
                    </p>
                @elseif($riskLevel === 'Gemiddeld')
                    <p class="mt-2 text-gray-700">
                        Deze regio heeft een gemiddeld risico. Het is aangeraden om de voorraad van kritieke materialen te controleren.
                    </p>
                @else
                    <p class="mt-2 text-gray-700">
                        Deze regio heeft momenteel een laag risico. Er zijn geen extra maatregelen nodig.
                    </p>
                @endif
            </div>
        @endif

        @if (count($dates) > 0)
            <div class="rounded-xl bg-white p-6 shadow">
                <h2 class="mb-4 text-xl font-semibold text-gray-900">
                    Dagelijkse voorspelling
                </h2>

                <div class="space-y-2">
                    @foreach ($dates as $index => $date)
                        <div class="flex justify-between border-b py-2 text-sm">
                            <span>{{ $date }}</span>
                            <span>{{ $rain[$index] }} mm neerslag</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
