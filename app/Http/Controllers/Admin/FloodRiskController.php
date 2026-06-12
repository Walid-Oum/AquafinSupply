<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class FloodRiskController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('province')->get();

        $provinceStats = [];

        foreach ($locations as $location) {
            $stats = $this->buildLocationWeatherStats($location);

            if ($stats !== null) {
                $provinceStats[] = $stats;
            }
        }

        return view('admin.flood-risk.index', [
            'locations' => $locations,
            'provinceStats' => $provinceStats,
        ]);
    }

    public function show(Location $location)
    {
        $selectedStats = $this->buildLocationWeatherStats($location);

        if ($selectedStats === null) {
            return redirect()
                ->route('admin.flood-risk.index')
                ->with('error', 'De weersgegevens konden tijdelijk niet opgehaald worden. Probeer later opnieuw.');
        }

        return view('admin.flood-risk.show', [
            'location' => $location,
            'selectedStats' => $selectedStats,
        ]);
    }

    private function determineRiskLevel(float $rainfall): string
    {
        if ($rainfall < 20) {
            return 'Laag';
        }

        if ($rainfall < 50) {
            return 'Gemiddeld';
        }

        return 'Hoog';
    }

    private function determineDailyRiskLevel(float $rainfall): string
    {
        if ($rainfall < 5) {
            return 'Laag';
        }

        if ($rainfall < 15) {
            return 'Gemiddeld';
        }

        return 'Hoog';
    }

    private function buildLocationWeatherStats(Location $location): ?array
    {
        try {
            $response = Http::timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_max,rain_sum,wind_gusts_10m_max',
                'timezone' => 'Europe/Brussels',
                'forecast_days' => 16,
            ]);
        } catch (\Exception $exception) {
            return null;
        }

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();

        if (! isset($data['daily']['time'], $data['daily']['precipitation_sum'])) {
            return null;
        }

        $dates = $data['daily']['time'];
        $rain = $data['daily']['precipitation_sum'];

        $currentWeekRain = array_sum(array_slice($rain, 0, 7));
        $nextWeekRain = array_sum(array_slice($rain, 7, 7));

        $nextWeekDates = array_slice($dates, 7, 7);
        $nextWeekDailyRain = array_slice($rain, 7, 7);

        $nextWeekStartDate = $nextWeekDates[0] ?? null;
        $nextWeekEndDate = $nextWeekDates[count($nextWeekDates) - 1] ?? null;

        $nextWeekPeriodLabel = null;

        if ($nextWeekStartDate && $nextWeekEndDate) {
            $nextWeekPeriodLabel =
                Carbon::parse($nextWeekStartDate)->locale('nl')->translatedFormat('d/m')
                . ' t.e.m. ' .
                Carbon::parse($nextWeekEndDate)->locale('nl')->translatedFormat('d/m');
        }

        $highestRainDay = 0;
        $highestRainDate = null;
        $highestRainDayLabel = null;

        if (count($nextWeekDailyRain) > 0) {
            $highestRainDay = max($nextWeekDailyRain);
            $highestRainIndex = array_search($highestRainDay, $nextWeekDailyRain);
            $highestRainDate = $nextWeekDates[$highestRainIndex] ?? null;

            if ($highestRainDate) {
                $highestRainDayLabel = ucfirst(
                    Carbon::parse($highestRainDate)->locale('nl')->translatedFormat('l d/m')
                );
            }
        }

        $riskDays = collect($nextWeekDailyRain)
            ->filter(fn ($dailyRain) => $dailyRain >= 15)
            ->count();

        $riskLevel = $this->determineRiskLevel($nextWeekRain);

        $nextWeekForecast = [];

        foreach ($nextWeekDates as $index => $date) {
            $dailyRain = $nextWeekDailyRain[$index] ?? 0;

            $nextWeekForecast[] = [
                'date' => $date,
                'dayLabel' => ucfirst(Carbon::parse($date)->locale('nl')->translatedFormat('l d/m')),
                'rain' => round($dailyRain, 1),
                'riskLevel' => $this->determineDailyRiskLevel($dailyRain),
            ];
        }

        return [
            'location' => $location,
            'province' => $location->province,
            'depot' => $location->name,
            'city' => $location->city,
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'currentWeekRain' => round($currentWeekRain, 1),
            'nextWeekRain' => round($nextWeekRain, 1),
            'nextWeekPeriodLabel' => $nextWeekPeriodLabel,
            'highestRainDay' => round($highestRainDay, 1),
            'highestRainDate' => $highestRainDate,
            'highestRainDayLabel' => $highestRainDayLabel,
            'riskDays' => $riskDays,
            'riskLevel' => $riskLevel,
            'dates' => $dates,
            'rain' => $rain,
            'nextWeekForecast' => $nextWeekForecast,
        ];
    }
}
