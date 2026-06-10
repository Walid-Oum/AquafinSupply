<x-app-layout>
    <div class="p-8">
        <div class="mb-6">
            <a href="{{ route('admin.flood-risk.index') }}" class="text-sm font-semibold text-[#0F4C81] hover:text-[#1E6BA8]">
                ← Terug naar overzicht
            </a>

            <div class="mt-4">
                <x-page-header title="Detailanalyse {{ $location->province }}" />

                <p class="mt-2 text-gray-600">
                    Bekijk de weersinschatting en materiaalplanning voor {{ $location->name }}.
                </p>
            </div>
        </div>

        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-4">
            <div class="rounded-xl bg-white p-6 shadow">
                <p class="text-sm text-gray-600">Neerslag volgende week</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">
                    {{ $selectedStats['nextWeekRain'] }} mm
                </p>
            </div>

            <div class="rounded-xl bg-white p-6 shadow">
                <p class="text-sm text-gray-600">Hoogste dagneerslag</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">
                    {{ $selectedStats['highestRainDay'] }} mm
                </p>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $selectedStats['highestRainDayLabel'] ?? 'Geen datum beschikbaar' }}
                </p>
            </div>

            <div class="rounded-xl bg-white p-6 shadow">
                <p class="text-sm text-gray-600">Risicodagen</p>
                <p class="mt-2 text-2xl font-bold text-gray-900">
                    {{ $selectedStats['riskDays'] }}
                </p>
            </div>

            <div class="rounded-xl bg-white p-6 shadow">
                <p class="text-sm text-gray-600">Prioriteit</p>

                <div class="mt-3">
                    @if($selectedStats['riskLevel'] === 'Hoog')
                        <span class="rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-700">
                            Hoog
                        </span>
                    @elseif($selectedStats['riskLevel'] === 'Gemiddeld')
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

        <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl bg-white p-6 shadow">
                <h2 class="text-xl font-semibold text-gray-900">
                    Depotinformatie
                </h2>

                <div class="mt-4 space-y-2 text-sm text-gray-700">
                    <p><strong>Provincie:</strong> {{ $location->province }}</p>
                    <p><strong>Depot:</strong> {{ $location->name }}</p>
                    <p><strong>Stad:</strong> {{ $location->city }}</p>
                    <p><strong>Adres:</strong> {{ $location->depot_address ?? 'Niet opgegeven' }}</p>
                    <p><strong>Coördinaten:</strong> {{ $location->latitude }}, {{ $location->longitude }}</p>
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow">
                <h2 class="text-xl font-semibold text-gray-900">
                    Administratief advies
                </h2>

                @if($selectedStats['riskLevel'] === 'Hoog')
                    <p class="mt-4 text-gray-700">
                        Deze provincie heeft een hoge prioriteit. Controleer de voorraad van overstromingsmateriaal en volg bestellingen voor dit depot extra goed op.
                    </p>
                @elseif($selectedStats['riskLevel'] === 'Gemiddeld')
                    <p class="mt-4 text-gray-700">
                        Deze provincie heeft een gemiddelde prioriteit. Het is aangeraden om kritieke voorraad en geplande bestellingen op te volgen.
                    </p>
                @else
                    <p class="mt-4 text-gray-700">
                        Deze provincie heeft momenteel een lage prioriteit. Er zijn geen extra acties nodig, maar de voorspelling blijft indicatief.
                    </p>
                @endif
            </div>
        </div>

        <div class="mb-6 rounded-xl bg-white p-6 shadow">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-900">
                    Dagelijkse voorspelling volgende week
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Overzicht van de verwachte neerslag per dag voor {{ $location->province }}.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Dag</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Neerslag</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Dagrisico</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($selectedStats['nextWeekForecast'] as $day)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $day['dayLabel'] }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $day['rain'] }} mm
                            </td>

                            <td class="px-4 py-3">
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
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-900">
                    Neerslagverloop volgende week
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Grafiek van de dagelijkse neerslag voor deze provincie.
                </p>
            </div>

            <div class="h-80">
                <canvas id="dailyRainChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const nextWeekForecast = @json($selectedStats['nextWeekForecast']);

        const dayLabels = nextWeekForecast.map(day => day.dayLabel);
        const dailyRain = nextWeekForecast.map(day => day.rain);

        const dailyRainChart = document.getElementById('dailyRainChart');

        if (dailyRainChart) {
            new Chart(dailyRainChart, {
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
