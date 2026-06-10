<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
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
                ->with('error', 'De weersgegevens konden niet opgehaald worden.');
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

    private function buildLocationWeatherStats(Location $location): ?array
    {
        $response = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_max,rain_sum,wind_gusts_10m_max',
            'timezone' => 'Europe/Brussels',
            'forecast_days' => 16,
        ]);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();

        $dates = $data['daily']['time'];
        $rain = $data['daily']['precipitation_sum'];

        $currentWeekRain = array_sum(array_slice($rain, 0, 7));
        $nextWeekRain = array_sum(array_slice($rain, 7, 7));
        $nextWeekDailyRain = array_slice($rain, 7, 7);

        $highestRainDay = 0;
        $highestRainDate = null;

        if (count($nextWeekDailyRain) > 0) {
            $highestRainDay = max($nextWeekDailyRain);
            $highestRainIndex = array_search($highestRainDay, $nextWeekDailyRain);
            $highestRainDate = $dates[7 + $highestRainIndex] ?? null;
        }

        $riskDays = collect($nextWeekDailyRain)
            ->filter(fn ($dailyRain) => $dailyRain >= 15)
            ->count();

        $riskLevel = $this->determineRiskLevel($nextWeekRain);

        return [
            'location' => $location,
            'province' => $location->province,
            'depot' => $location->name,
            'city' => $location->city,
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'currentWeekRain' => round($currentWeekRain, 1),
            'nextWeekRain' => round($nextWeekRain, 1),
            'highestRainDay' => round($highestRainDay, 1),
            'highestRainDate' => $highestRainDate,
            'riskDays' => $riskDays,
            'riskLevel' => $riskLevel,
            'dates' => $dates,
            'rain' => $rain,
        ];
    }
}
