<x-app-layout>
    <div class="p-8">
        <div class="mb-6">
            <x-page-header title="Overstromingsrisico voor jouw depot" />

            <p class="mt-2 text-gray-600">
                Bekijk het indicatieve weekoverzicht voor jouw gekoppelde depot. Deze informatie helpt bij materiaalvoorbereiding en planning.
            </p>
        </div>

        @if($fromCache ?? false)
            <div class="mb-6 rounded-lg bg-yellow-100 border-l-4 border-yellow-500 p-4 text-yellow-800">
                Live weersgegevens zijn tijdelijk niet beschikbaar. We tonen de laatst opgeslagen gegevens voor jouw depot.
            </div>
        @endif

        @if ($error && !($fromCache ?? false))
            <div class="mb-6 rounded-lg bg-red-100 p-4 text-red-700">
                {{ $error }}
            </div>
        @endif

        @if ($location)
            <div class="mb-6 rounded-xl bg-white p-6 shadow">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">
                            {{ $location->province }} - {{ $location->name }}
                        </h2>

                        <p class="mt-1 text-gray-600">
                            Depot: {{ $location->name }}
                        </p>

                        <p class="mt-1 text-gray-600">
                            Stad: {{ $location->city }}
                        </p>

                        @if($location->depot_address)
                            <p class="mt-1 text-gray-600">
                                Adres: {{ $location->depot_address }}
                            </p>
                        @endif
                    </div>

                    @if($riskLevel)
                        <div>
                            @if($riskLevel === 'Hoog')
                                <span class="rounded-full bg-red-100 px-4 py-2 text-sm font-semibold text-red-700">
                                    Hoge prioriteit
                                </span>
                            @elseif($riskLevel === 'Gemiddeld')
                                <span class="rounded-full bg-yellow-100 px-4 py-2 text-sm font-semibold text-yellow-700">
                                    Gemiddelde prioriteit
                                </span>
                            @else
                                <span class="rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-700">
                                    Lage prioriteit
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if ($riskLevel)
            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="rounded-xl bg-white p-6 shadow">
                    <p class="text-sm text-gray-600">
                        Verwachte neerslag
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ $weekRain }} mm
                    </p>

                    @if($periodLabel)
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $periodLabel }}
                        </p>
                    @endif
                </div>

                <div class="rounded-xl bg-white p-6 shadow">
                    <p class="text-sm text-gray-600">
                        Hoogste dagneerslag
                    </p>

                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ $highestRainDay }} mm
                    </p>

                    <p class="mt-1 text-sm text-gray-500">
                        {{ $highestRainDayLabel ?? 'Geen datum beschikbaar' }}
                    </p>
                </div>

                <div class="rounded-xl bg-white p-6 shadow">
                    <p class="text-sm text-gray-600">
                        Weekprioriteit
                    </p>

                    <div class="mt-3">
                        @if($riskLevel === 'Hoog')
                            <span class="rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-700">
                                Hoog
                            </span>
                        @elseif($riskLevel === 'Gemiddeld')
                            <span class="rounded-full bg-yellow-100 px-3 py-1 text-sm font-semibold text-yellow-700">
                                Gemiddeld
                            </span>
                        @else
                            <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700">
                                Laag
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mb-6 rounded-xl bg-white p-6 shadow">
                <h2 class="text-xl font-semibold text-gray-900">
                    Advies voor deze week
                </h2>

                @if($role === 'technieker')
                    @if($riskLevel === 'Hoog')
                        <p class="mt-3 text-gray-700">
                            Er is een hoge prioriteit voor jouw depot. Controleer vóór vertrek of je voldoende materiaal bij hebt voor wateroverlast, zoals regenmateriaal, werklaarzen, slangen en indien nodig een dompelpomp of rioolstop.
                        </p>
                    @elseif($riskLevel === 'Gemiddeld')
                        <p class="mt-3 text-gray-700">
                            Er is een gemiddelde prioriteit voor jouw depot. Controleer je materiaal extra goed en hou rekening met mogelijke regen of wateroverlast tijdens interventies.
                        </p>
                    @else
                        <p class="mt-3 text-gray-700">
                            Er is momenteel een lage prioriteit voor jouw depot. Neem je standaardmateriaal mee en volg de dagelijkse veiligheidsafspraken.
                        </p>
                    @endif

                    <div class="mt-4 rounded-lg bg-blue-50 p-4 text-sm text-blue-800">
                        Veiligheidsherinnering: neem altijd je gasdetectiemeter mee voor vertrek naar een interventie.
                    </div>
                @else
                    @if($riskLevel === 'Hoog')
                        <p class="mt-3 text-gray-700">
                            Er is een hoge prioriteit voor dit depot. Controleer de voorraad van overstromingsmateriaal en zorg dat kritieke materialen snel beschikbaar zijn voor techniekers.
                        </p>
                    @elseif($riskLevel === 'Gemiddeld')
                        <p class="mt-3 text-gray-700">
                            Er is een gemiddelde prioriteit voor dit depot. Volg de voorraad van kritieke materialen op en controleer of veelgevraagd materiaal voldoende beschikbaar is.
                        </p>
                    @else
                        <p class="mt-3 text-gray-700">
                            Er is momenteel een lage prioriteit voor dit depot. Er zijn geen extra voorraadacties nodig, maar de voorspelling blijft indicatief.
                        </p>
                    @endif
                @endif
            </div>
        @endif

        @if(count($weekForecast) > 0)
            <div class="mb-6 rounded-xl bg-white p-6 shadow">
                <div class="mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">
                        Weekoverzicht
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        Overzicht van de verwachte neerslag per dag voor jouw depot.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    @foreach($weekForecast as $day)
                        <div class="rounded-xl border border-gray-200 p-4">
                            <p class="font-semibold text-gray-900">
                                {{ $day['dayLabel'] }}
                            </p>

                            <p class="mt-2 text-2xl font-bold text-gray-900">
                                {{ $day['rain'] }} mm
                            </p>

                            <div class="mt-3">
                                @if($day['riskLevel'] === 'Hoog')
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        Hoog
                                    </span>
                                @elseif($day['riskLevel'] === 'Gemiddeld')
                                    <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                        Gemiddeld
                                    </span>
                                @else
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Laag
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow">
                <div class="mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">
                        Neerslagverloop komende 7 dagen
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        Grafiek van de dagelijkse neerslag voor jouw gekoppelde depot.
                    </p>
                </div>

                <div class="h-80">
                    <canvas id="weeklyRainChart"></canvas>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const weekForecast = @json($weekForecast);

        const dayLabels = weekForecast.map(day => day.dayLabel);
        const dailyRain = weekForecast.map(day => day.rain);

        const weeklyRainChart = document.getElementById('weeklyRainChart');

        if (weeklyRainChart) {
            new Chart(weeklyRainChart, {
                type: 'bar',
                data: {
                    labels: dayLabels,
                    datasets: [{
                        label: 'Neerslag per dag (mm)',
                        data: dailyRain,
                        backgroundColor: '#0F4C81',
                        borderColor: '#0F4C81',
                        borderWidth: 1,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.raw + ' mm';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Neerslag (mm)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Dag'
                            }
                        }
                    }
                }
            });
        }
    </script>
</x-app-layout>
