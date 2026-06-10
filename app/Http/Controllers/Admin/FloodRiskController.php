<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FloodRiskController extends Controller
{
    public function index(Request $request)
    {
        $locations = Location::orderBy('province')->get();

        $provinceStats = [];

        foreach ($locations as $location) {
            $stats = $this->buildLocationWeatherStats($location);

            if ($stats !== null) {
                $provinceStats[] = $stats;
            }
        }

        $selectedLocation = null;

        if ($locations->isNotEmpty()) {
            if ($request->filled('location_id')) {
                $selectedLocation = Location::findOrFail($request->location_id);
            } else {
                $selectedLocation = $locations->first();
            }
        }

        if (! $selectedLocation) {
            return view('admin.flood-risk.index', [
                'locations' => $locations,
                'selectedLocation' => null,
                'error' => 'Er zijn nog geen locaties beschikbaar.',
                'dates' => [],
                'rain' => [],
                'currentWeekRain' => null,
                'nextWeekRain' => null,
                'riskLevel' => null,
                'provinceStats' => $provinceStats,
            ]);
        }

        $selectedStats = $this->buildLocationWeatherStats($selectedLocation);

        if ($selectedStats === null) {
            return view('admin.flood-risk.index', [
                'locations' => $locations,
                'selectedLocation' => $selectedLocation,
                'error' => 'De weersgegevens konden niet opgehaald worden.',
                'dates' => [],
                'rain' => [],
                'currentWeekRain' => null,
                'nextWeekRain' => null,
                'riskLevel' => null,
                'provinceStats' => $provinceStats,
            ]);
        }

        return view('admin.flood-risk.index', [
            'locations' => $locations,
            'selectedLocation' => $selectedLocation,
            'error' => null,
            'dates' => $selectedStats['dates'],
            'rain' => $selectedStats['rain'],
            'currentWeekRain' => $selectedStats['currentWeekRain'],
            'nextWeekRain' => $selectedStats['nextWeekRain'],
            'riskLevel' => $selectedStats['riskLevel'],
            'provinceStats' => $provinceStats,
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

        $highestRainDay = count($nextWeekDailyRain) > 0 ? max($nextWeekDailyRain) : 0;

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
            'riskDays' => $riskDays,
            'riskLevel' => $riskLevel,
            'dates' => $dates,
            'rain' => $rain,
        ];
    }
}
