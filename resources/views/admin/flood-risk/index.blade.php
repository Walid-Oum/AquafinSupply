{{--
    Pagina: Overstromingsrisico per provincie

    Doel:
    Geeft admins een overzicht van het verwachte
    overstromingsrisico per provincie en depot.

    Functionaliteiten:
    - Overzicht van alle provinciale depots
    - Weergave van voorspelde neerslag
    - Risicobeoordeling (Laag, Gemiddeld, Hoog)
    - Aanbevolen acties per risiconiveau
    - Grafieken met neerslag- en prioriteitsgegevens
    - Fallback naar gecachte weersgegevens bij API-problemen

    Gebruikersrol:
    - Admin
--}}

<x-app-layout>
    <div class="min-w-0 max-w-full space-y-6 overflow-x-hidden">
        {{-- HEADER --}}
        <div>
            <x-page-header title="Overstromingsrisico per provincie" />

            <p class="mt-2 text-sm text-gray-600 sm:text-base">
                Bekijk de algemene risicosituatie per provinciaal depot.
            </p>
        </div>

        {{-- CACHE MELDING --}}
        @if(collect($provinceStats)->contains(fn($stats) => $stats['fromCache'] ?? false))
            <div class="rounded-2xl border-l-4 border-yellow-500 bg-yellow-100 p-4 text-sm text-yellow-800">
                Live weersgegevens zijn tijdelijk niet beschikbaar. We tonen voor sommige provincies de laatst opgeslagen gegevens.
            </div>
        @endif

        {{-- ERROR MELDING --}}
        @if(session('error'))
            <div class="rounded-2xl bg-red-100 p-4 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if(count($provinceStats) > 0)
            {{-- OVERZICHT --}}
            <section class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
                <div class="mb-5">
                    <h2 class="text-xl font-bold text-gray-900">
                        Algemeen overzicht per provincie
                    </h2>

                    <p class="mt-1 text-sm leading-relaxed text-gray-600">
                        Dit overzicht helpt admins om te bepalen waar extra materiaalvoorbereiding of voorraadcontrole nodig is.
                    </p>
                </div>

                {{-- DESKTOPTABEL --}}
                <div class="hidden overflow-hidden rounded-2xl border border-gray-100 lg:block">
                    <table class="w-full table-fixed divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="w-[15%] px-4 py-3 text-left font-semibold text-gray-700">
                                Provincie
                            </th>

                            <th class="w-[17%] px-4 py-3 text-left font-semibold text-gray-700">
                                Depot
                            </th>

                            <th class="w-[15%] px-4 py-3 text-left font-semibold text-gray-700">
                                Neerslag volgende week
                            </th>

                            <th class="w-[15%] px-4 py-3 text-left font-semibold text-gray-700">
                                Piekdag
                            </th>

                            <th class="w-[10%] px-4 py-3 text-left font-semibold text-gray-700">
                                Risicodagen
                            </th>

                            <th class="w-[12%] px-4 py-3 text-left font-semibold text-gray-700">
                                Prioriteit
                            </th>

                            <th class="w-[16%] px-4 py-3 text-left font-semibold text-gray-700">
                                Aanbevolen actie
                            </th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($provinceStats as $stats)
                            <tr class="transition hover:bg-gray-50/70">
                                <td class="px-4 py-4 font-semibold text-gray-900">
                                    <div class="truncate">
                                        {{ $stats['province'] }}
                                    </div>
                                </td>

                                <td class="px-4 py-4 text-gray-600">
                                    <div class="truncate">
                                        {{ $stats['depot'] }}
                                    </div>

                                    <p class="text-xs text-gray-400">
                                        {{ $stats['city'] }}
                                    </p>
                                </td>

                                <td class="px-4 py-4 text-gray-700">
                                        <span class="font-bold">
                                            {{ $stats['nextWeekRain'] }} mm
                                        </span>

                                    @if(!empty($stats['nextWeekPeriodLabel']))
                                        <p class="text-xs text-gray-400">
                                            {{ $stats['nextWeekPeriodLabel'] }}
                                        </p>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-gray-700">
                                        <span class="font-bold">
                                            {{ $stats['highestRainDay'] }} mm
                                        </span>

                                    <p class="text-xs text-gray-400">
                                        {{ $stats['highestRainDayLabel'] ?? 'Geen datum' }}
                                    </p>
                                </td>

                                <td class="px-4 py-4 text-gray-700">
                                    {{ $stats['riskDays'] }}
                                </td>

                                <td class="px-4 py-4">
                                    @if($stats['riskLevel'] === 'Hoog')
                                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                                Hoog
                                            </span>
                                    @elseif($stats['riskLevel'] === 'Gemiddeld')
                                        <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                                Gemiddeld
                                            </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                                Laag
                                            </span>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-sm">
                                    @if($stats['riskLevel'] === 'Hoog')
                                        <span class="font-semibold text-red-700">
                                                Controleer voorraad en bereid extra materiaal voor.
                                            </span>
                                    @elseif($stats['riskLevel'] === 'Gemiddeld')
                                        <span class="font-semibold text-yellow-700">
                                                Volg kritieke voorraad extra op.
                                            </span>
                                    @else
                                        <span class="font-semibold text-green-700">
                                                Geen extra actie nodig.
                                            </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- MOBIELE KAARTEN --}}
                <div class="space-y-4 lg:hidden">
                    @foreach($provinceStats as $stats)
                        <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                            <div class="flex flex-col gap-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">
                                            {{ $stats['province'] }}
                                        </h3>

                                        <p class="text-sm text-gray-500">
                                            {{ $stats['depot'] }} - {{ $stats['city'] }}
                                        </p>
                                    </div>

                                    <div class="shrink-0">
                                        @if($stats['riskLevel'] === 'Hoog')
                                            <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                                Hoog
                                            </span>
                                        @elseif($stats['riskLevel'] === 'Gemiddeld')
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

                                <div class="grid gap-2 text-sm text-gray-700">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-gray-500">
                                            Neerslag volgende week
                                        </span>

                                        <span class="font-bold">
                                            {{ $stats['nextWeekRain'] }} mm
                                        </span>
                                    </div>

                                    @if(!empty($stats['nextWeekPeriodLabel']))
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-gray-500">
                                                Periode
                                            </span>

                                            <span class="text-right font-medium">
                                                {{ $stats['nextWeekPeriodLabel'] }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-gray-500">
                                            Piekdag
                                        </span>

                                        <span class="text-right font-medium">
                                            {{ $stats['highestRainDayLabel'] ?? 'Geen datum' }}
                                            <span class="font-bold">
                                                {{ $stats['highestRainDay'] }} mm
                                            </span>
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between gap-3">
                                        <span class="text-gray-500">
                                            Risicodagen
                                        </span>

                                        <span class="font-bold">
                                            {{ $stats['riskDays'] }}
                                        </span>
                                    </div>
                                </div>

                                <div class="rounded-xl bg-gray-50 p-3 text-sm">
                                    <p class="mb-1 font-semibold text-gray-700">
                                        Aanbevolen actie
                                    </p>

                                    @if($stats['riskLevel'] === 'Hoog')
                                        <p class="font-semibold text-red-700">
                                            Controleer voorraad en bereid extra materiaal voor.
                                        </p>
                                    @elseif($stats['riskLevel'] === 'Gemiddeld')
                                        <p class="font-semibold text-yellow-700">
                                            Volg kritieke voorraad extra op.
                                        </p>
                                    @else
                                        <p class="font-semibold text-green-700">
                                            Geen extra actie nodig.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- GRAFIEKEN --}}
            <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
                    <div class="mb-4">
                        <h2 class="text-xl font-bold text-gray-900">
                            Neerslag volgende week
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            Totale voorspelde neerslag per provincie.
                        </p>
                    </div>

                    <div class="h-72">
                        <canvas id="rainfallChart"></canvas>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6">
                    <div class="mb-4">
                        <h2 class="text-xl font-bold text-gray-900">
                            Hoogste dagneerslag
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            Hoogste verwachte dagneerslag per provincie.
                        </p>
                    </div>

                    <div class="h-72">
                        <canvas id="peakRainChart"></canvas>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-6 xl:col-span-2">
                    <div class="mb-4">
                        <h2 class="text-xl font-bold text-gray-900">
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
            </section>
        @else
            <div class="rounded-2xl border border-gray-100 bg-white p-6 text-center text-gray-500 shadow-sm">
                Er zijn momenteel geen risicogegevens beschikbaar.
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
