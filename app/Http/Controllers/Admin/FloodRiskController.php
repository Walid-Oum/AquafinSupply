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
        $locations = Location::orderBy('city')->get();

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
            ]);
        }

        $response = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $selectedLocation->latitude,
            'longitude' => $selectedLocation->longitude,
            'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_max,rain_sum,wind_gusts_10m_max',
            'timezone' => 'Europe/Brussels',
            'forecast_days' => 16,
        ]);

        if ($response->failed()) {
            return view('admin.flood-risk.index', [
                'locations' => $locations,
                'selectedLocation' => $selectedLocation,
                'error' => 'De weersgegevens konden niet opgehaald worden.',
                'dates' => [],
                'rain' => [],
                'currentWeekRain' => null,
                'nextWeekRain' => null,
                'riskLevel' => null,
            ]);
        }

        $data = $response->json();

        $dates = $data['daily']['time'];
        $rain = $data['daily']['precipitation_sum'];

        $currentWeekRain = array_sum(array_slice($rain, 0, 7));
        $nextWeekRain = array_sum(array_slice($rain, 7, 7));

        $riskLevel = $this->determineRiskLevel($nextWeekRain);

        return view('admin.flood-risk.index', [
            'locations' => $locations,
            'selectedLocation' => $selectedLocation,
            'error' => null,
            'dates' => $dates,
            'rain' => $rain,
            'currentWeekRain' => round($currentWeekRain, 1),
            'nextWeekRain' => round($nextWeekRain, 1),
            'riskLevel' => $riskLevel,
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
}
