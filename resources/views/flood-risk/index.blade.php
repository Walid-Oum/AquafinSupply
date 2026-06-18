{{--

Pagina: Overstromingsrisico

Beschrijving:
Toont een indicatief overzicht van het overstromingsrisico voor het gekoppelde depot van de gebruiker op basis van weersvoorspellingen.

Functionaliteiten:
- Weergeven van depotinformatie
- Tonen van risico- en prioriteitsniveaus
- Genereren van rolgebonden advies voor medewerkers
- Overzicht van verwachte neerslag voor de komende week
- Visualisatie van neerslaggegevens via een grafiek

--}}
<x-app-layout>
    <div class="space-y-6">
        <div>
            <x-page-header title="Overstromingsrisico voor jouw depot" />

            <p class="mt-1 text-sm text-gray-600 sm:text-base">
                Bekijk het indicatieve weekoverzicht voor jouw gekoppelde depot. Deze informatie helpt bij materiaalvoorbereiding en planning.
            </p>
        </div>

        @if($fromCache ?? false)
            <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-4 text-yellow-800 shadow-sm">
                <p class="font-semibold">
                    Live weersgegevens tijdelijk niet beschikbaar
                </p>

                <p class="mt-1 text-sm">
                    We tonen de laatst opgeslagen gegevens voor jouw depot.
                </p>
            </div>
        @endif

        @if ($error && !($fromCache ?? false))
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700 shadow-sm">
                {{ $error }}
            </div>
        @endif

        @if ($location)
            <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-gray-400">
                            Depot
                        </p>

                        <h2 class="mt-1 text-2xl font-bold text-[#0F4C81]">
                            {{ $location->province }} - {{ $location->name }}
                        </h2>

                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div>
                                <p class="text-sm font-semibold text-gray-500">
                                    Depot
                                </p>

                                <p class="mt-1 font-semibold text-gray-900">
                                    {{ $location->name }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-gray-500">
                                    Stad
                                </p>

                                <p class="mt-1 font-semibold text-gray-900">
                                    {{ $location->city }}
                                </p>
                            </div>

                            @if($location->depot_address)
                                <div class="sm:col-span-2">
                                    <p class="text-sm font-semibold text-gray-500">
                                        Adres
                                    </p>

                                    <p class="mt-1 font-semibold text-gray-900">
                                        {{ $location->depot_address }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($riskLevel)
                        <div class="shrink-0">
                            @if($riskLevel === 'Hoog')
                                <span class="inline-flex rounded-full bg-red-100 px-4 py-2 text-sm font-semibold text-red-700">
                                    Hoge prioriteit
                                </span>
                            @elseif($riskLevel === 'Gemiddeld')
                                <span class="inline-flex rounded-full bg-yellow-100 px-4 py-2 text-sm font-semibold text-yellow-700">
                                    Gemiddelde prioriteit
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-700">
                                    Lage prioriteit
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </section>
        @endif

        @if ($riskLevel)
            <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                    <p class="text-sm font-semibold text-gray-500">
                        Verwachte neerslag
                    </p>

                    <p class="mt-2 text-3xl font-bold text-[#0F4C81]">
                        {{ $weekRain }} mm
                    </p>

                    @if($periodLabel)
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $periodLabel }}
                        </p>
                    @endif
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                    <p class="text-sm font-semibold text-gray-500">
                        Hoogste dagneerslag
                    </p>

                    <p class="mt-2 text-3xl font-bold text-[#0F4C81]">
                        {{ $highestRainDay }} mm
                    </p>

                    <p class="mt-1 text-sm text-gray-500">
                        {{ $highestRainDayLabel ?? 'Geen datum beschikbaar' }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                    <p class="text-sm font-semibold text-gray-500">
                        Weekprioriteit
                    </p>

                    <div class="mt-3">
                        @if($riskLevel === 'Hoog')
                            <span class="inline-flex rounded-full bg-red-100 px-4 py-2 text-sm font-semibold text-red-700">
                                Hoog
                            </span>
                        @elseif($riskLevel === 'Gemiddeld')
                            <span class="inline-flex rounded-full bg-yellow-100 px-4 py-2 text-sm font-semibold text-yellow-700">
                                Gemiddeld
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-700">
                                Laag
                            </span>
                        @endif
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
                <h2 class="text-xl font-bold text-[#0F4C81]">
                    Advies voor deze week
                </h2>

                @if($role === 'technieker')
                    @if($riskLevel === 'Hoog')
                        <p class="mt-3 leading-relaxed text-gray-700">
                            Er is een hoge prioriteit voor jouw depot. Controleer vóór vertrek of je voldoende materiaal bij hebt voor wateroverlast, zoals regenmateriaal, werklaarzen, slangen en indien nodig een dompelpomp of rioolstop.
                        </p>
                    @elseif($riskLevel === 'Gemiddeld')
                        <p class="mt-3 leading-relaxed text-gray-700">
                            Er is een gemiddelde prioriteit voor jouw depot. Controleer je materiaal extra goed en hou rekening met mogelijke regen of wateroverlast tijdens interventies.
                        </p>
                    @else
                        <p class="mt-3 leading-relaxed text-gray-700">
                            Er is momenteel een lage prioriteit voor jouw depot. Neem je standaardmateriaal mee en volg de dagelijkse veiligheidsafspraken.
                        </p>
                    @endif

                    <div class="mt-4 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                        <p class="font-semibold">
                            Veiligheidsherinnering
                        </p>

                        <p class="mt-1">
                            Neem altijd je gasdetectiemeter mee voor vertrek naar een interventie.
                        </p>
                    </div>
                @else
                    @if($riskLevel === 'Hoog')
                        <p class="mt-3 leading-relaxed text-gray-700">
                            Er is een hoge prioriteit voor dit depot. Controleer de voorraad van overstromingsmateriaal en zorg dat kritieke materialen snel beschikbaar zijn voor techniekers.
                        </p>
                    @elseif($riskLevel === 'Gemiddeld')
                        <p class="mt-3 leading-relaxed text-gray-700">
                            Er is een gemiddelde prioriteit voor dit depot. Volg de voorraad van kritieke materialen op en controleer of veelgevraagd materiaal voldoende beschikbaar is.
                        </p>
                    @else
                        <p class="mt-3 leading-relaxed text-gray-700">
                            Er is momenteel een lage prioriteit voor dit depot. Er zijn geen extra voorraadacties nodig, maar de voorspelling blijft indicatief.
                        </p>
                    @endif
                @endif
            </section>
        @endif

        @if(count($weekForecast) > 0)
            <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-4 py-4 sm:px-6">
                    <h2 class="text-xl font-bold text-[#0F4C81]">
                        Weekoverzicht
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        Overzicht van de verwachte neerslag per dag voor jouw depot.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 sm:p-6">
                    @foreach($weekForecast as $day)
                        <article class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-bold text-gray-900">
                                        {{ $day['dayLabel'] }}
                                    </p>

                                    <p class="mt-2 text-2xl font-bold text-[#0F4C81]">
                                        {{ $day['rain'] }} mm
                                    </p>
                                </div>

                                <div class="shrink-0">
                                    @if($day['riskLevel'] === 'Hoog')
                                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                            Hoog
                                        </span>
                                    @elseif($day['riskLevel'] === 'Gemiddeld')
                                        <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                            Gemiddeld
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            Laag
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-[#0F4C81]">
                        Neerslagverloop komende 7 dagen
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        Grafiek van de dagelijkse neerslag voor jouw gekoppelde depot.
                    </p>
                </div>

                <div class="h-72 sm:h-80">
                    <canvas id="weeklyRainChart"></canvas>
                </div>
            </section>
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
