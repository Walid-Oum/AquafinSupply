<x-app-layout>
    <div class="p-8">
        <div class="mb-6">
            <x-page-header title="Overstromingsrisico per provincie" />

            <p class="mt-2 text-gray-600">
                Bekijk de algemene risicosituatie per provinciaal depot.
            </p>
        </div>

        @if(collect($provinceStats)->contains(fn($stats) => $stats['fromCache'] ?? false))
            <div class="mb-6 rounded-lg bg-yellow-100 border-l-4 border-yellow-500 p-4 text-yellow-800">
                Live weersgegevens zijn tijdelijk niet beschikbaar. We tonen voor sommige provincies de laatst opgeslagen gegevens.
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-lg bg-red-100 p-4 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if(count($provinceStats) > 0)
            <div class="mb-6 rounded-xl bg-white p-6 shadow">
                <div class="mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">
                        Algemeen overzicht per provincie
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        Dit overzicht helpt admins om te bepalen waar extra materiaalvoorbereiding of voorraadcontrole nodig is.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Provincie</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Depot</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Neerslag volgende week</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Piekdag</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Risicodagen</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Prioriteit</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Aanbevolen actie</th>

                        </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($provinceStats as $stat)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ $stat['province'] }}
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    {{ $stat['depot'] }}
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    {{ $stat['nextWeekRain'] }} mm
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    @if($stat['highestRainDayLabel'])
                                        {{ $stat['highestRainDayLabel'] }} - {{ $stat['highestRainDay'] }} mm
                                    @else
                                        {{ $stat['highestRainDay'] }} mm
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    {{ $stat['riskDays'] }}
                                </td>

                                <td class="px-4 py-3">
                                    @if($stat['riskLevel'] === 'Hoog')
                                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                            Hoog
                                        </span>
                                    @elseif($stat['riskLevel'] === 'Gemiddeld')
                                        <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                            Gemiddeld
                                        </span>
                                    @else
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            Laag
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">

                                    @if($stat['riskLevel'] === 'Hoog')

                                        <div class="text-red-700">
                                            ⚠ Controleer voorraad overstromingsmateriaal.<br>
                                            ⚠ Plan preventiev-e interventies.<br>
                                            ⚠ Controleer pompen en noodmateriaal.
                                        </div>

                                    @elseif($stat['riskLevel'] === 'Gemiddeld')

                                        <div class="text-yellow-700">
                                            ⚠ Volg kritieke voorraad extra op.<br>
                                            ⚠ Controleer voorspellingen dagelijks.
                                        </div>

                                    @else

                                        <div class="text-green-700">
                                            ✓ Geen extra actie nodig.
                                        </div>

                                    @endif

                                </td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="mb-6 rounded-xl bg-white p-6 shadow">
                <p class="text-gray-600">
                    Er zijn momenteel geen weergegevens beschikbaar.
                </p>
            </div>
        @endif

        @if(count($provinceStats) > 0)
            @php
                $periodLabel = $provinceStats[0]['nextWeekPeriodLabel'] ?? null;
            @endphp

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="rounded-xl bg-white p-6 shadow">
                    <div class="mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">
                            Neerslag per provincie
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            Verwachte totale neerslag per provinciaal depot
                            @if($periodLabel)
                                voor de periode {{ $periodLabel }}.
                            @else
                                voor volgende week.
                            @endif
                        </p>
                    </div>

                    <div class="h-72">
                        <canvas id="rainfallChart"></canvas>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow">
                    <div class="mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">
                            Hoogste dagneerslag per provincie
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            Toont per provincie de natste voorspelde dag
                            @if($periodLabel)
                                binnen de periode {{ $periodLabel }}.
                            @else
                                van volgende week.
                            @endif
                        </p>
                    </div>

                    <div class="h-72">
                        <canvas id="peakRainChart"></canvas>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow">
                    <div class="mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">
                            Prioriteitenverdeling
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            Aantal provincies met lage, gemiddelde of hoge prioriteit.
                        </p>
                    </div>

                    <div class="mx-auto h-64 max-w-xs">
                        <canvas id="priorityChart"></canvas>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const provinceStats = @json($provinceStats);

        const provinceLabels = provinceStats.map(stat => stat.province);
        const nextWeekRain = provinceStats.map(stat => stat.nextWeekRain);
        const highestRainDay = provinceStats.map(stat => stat.highestRainDay);
        const highestRainDayLabel = provinceStats.map(stat => stat.highestRainDayLabel);

        const riskCounts = {
            Laag: provinceStats.filter(stat => stat.riskLevel === 'Laag').length,
            Gemiddeld: provinceStats.filter(stat => stat.riskLevel === 'Gemiddeld').length,
            Hoog: provinceStats.filter(stat => stat.riskLevel === 'Hoog').length,
        };

        const rainfallChart = document.getElementById('rainfallChart');

        if (rainfallChart) {
            new Chart(rainfallChart, {
                type: 'bar',
                data: {
                    labels: provinceLabels,
                    datasets: [{
                        label: 'Neerslag volgende week (mm)',
                        data: nextWeekRain,
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
                                text: 'Provincie'
                            }
                        }
                    }
                }
            });
        }

        const peakRainChart = document.getElementById('peakRainChart');

        if (peakRainChart) {
            new Chart(peakRainChart, {
                type: 'bar',
                data: {
                    labels: provinceLabels,
                    datasets: [{
                        label: 'Hoogste dagneerslag (mm)',
                        data: highestRainDay,
                        backgroundColor: '#1E6BA8',
                        borderColor: '#1E6BA8',
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
                                    const day = highestRainDayLabel[context.dataIndex];

                                    if (day) {
                                        return day + ': ' + context.raw + ' mm';
                                    }

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
                                text: 'Provincie'
                            }
                        }
                    }
                }
            });
        }

        const priorityChart = document.getElementById('priorityChart');

        if (priorityChart) {
            new Chart(priorityChart, {
                type: 'doughnut',
                data: {
                    labels: ['Laag', 'Gemiddeld', 'Hoog'],
                    datasets: [{
                        label: 'Aantal provincies',
                        data: [
                            riskCounts.Laag,
                            riskCounts.Gemiddeld,
                            riskCounts.Hoog
                        ],
                        backgroundColor: [
                            '#BBF7D0',
                            '#FEF3C7',
                            '#FECACA'
                        ],
                        borderColor: [
                            '#22C55E',
                            '#F59E0B',
                            '#EF4444'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 16,
                                padding: 16,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw + ' provincie(s)';
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</x-app-layout>
