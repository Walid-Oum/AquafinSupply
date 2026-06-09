<x-app-layout>
    <div class="p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Overstromingsrisico
            </h1>

            @if(auth()->user()->role === 'technieker')
                <p class="text-gray-600">
                    Bekijk hier het risico voor jouw werkregio. Dit helpt je om materiaal voor volgende week te kiezen.
                </p>
            @elseif(auth()->user()->role === 'magazijn')
                <p class="text-gray-600">
                    Bekijk hier het risico voor jouw magazijnregio. Dit helpt je om de voorraad tijdig voor te bereiden.
                </p>
            @endif
        </div>

        @if ($error)
            <div class="mb-6 rounded-lg bg-red-100 p-4 text-red-700">
                {{ $error }}
            </div>
        @endif

        @if ($location)
            <div class="mb-6 rounded-xl bg-white p-6 shadow">
                <h2 class="text-xl font-semibold text-gray-900">
                    Locatie: {{ $location->name }}
                </h2>

                <p class="mt-1 text-gray-600">
                    Stad: {{ $location->city }}
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
                    Advies
                </h2>

                @if($riskLevel === 'Hoog')
                    <p class="mt-2 text-gray-700">
                        Er wordt veel neerslag verwacht. Voorzie extra overstromingsmateriaal zoals dompelpompen, rioolstoppen, slangenwagens, regenmateriaal en werklaarzen.
                    </p>
                @elseif($riskLevel === 'Gemiddeld')
                    <p class="mt-2 text-gray-700">
                        Er is een verhoogde kans op wateroverlast. Controleer de voorraad van pompen, slangen, koppelingen en regenmateriaal.
                    </p>
                @else
                    <p class="mt-2 text-gray-700">
                        Er is momenteel een laag risico. De normale voorraad volstaat.
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

                            <span>
                                {{ $rain[$index] }} mm neerslag
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
