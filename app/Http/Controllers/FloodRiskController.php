<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FloodRiskController extends Controller
{
    /** Toon de overstromingsrisico's dashboard op basis van de weergegevens */
    public function index()
    {
        /** Haal de ingelogde gebruiker op */
        $user = auth()->user();
        $location = $user->location;
        /** foutafhandeling: als er geen locatie is gekoppeld aan de gebruiker */
        if (!$location) {
            return view('flood-risk.index', [
                'error' => 'Er is geen depot gekoppeld aan deze gebruiker.',
                'location' => null,
                'weekRain' => null,
                'riskLevel' => null,
                'weekForecast' => [],
                'periodLabel' => null,
                'highestRainDay' => null,
                'highestRainDayLabel' => null,
                'role' => $user->role,
                'fromCache' => false,
            ]);
        }
/** unieke cache sleutel opbouw per locatie */
        $cacheKey = 'flood_risk_weather_' . $location->id;
        $fromCache = false;

        try {
            /** doe een live Api aanroep naar Open-Meteo met een timeout van 10 seconden */
            $response = Http::timeout(10)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_max,rain_sum,wind_gusts_10m_max',
                'timezone' => 'Europe/Brussels',
                'forecast_days' => 7,
            ]);
/** gooi een exceptie als de statuscode geen succes is */
            if ($response->failed()) {
                throw new \Exception('Weather API request failed.');
            }

            $data = $response->json();
            /** valideer of de benodigde arrays aanwezig zijn */

            if (!isset($data['daily']['time'], $data['daily']['precipitation_sum'])) {
                throw new \Exception('Weather API data incomplete.');
            }
/** sla de gegevens op in de cache */
            Cache::put($cacheKey, $data, now()->addDay());
        } catch (\Exception $exception) {
            $data = Cache::get($cacheKey);
/**als er ook geen cache beschikbaar is, toon dan een specifieke foutmelding */
            if (!$data) {
                return view('flood-risk.index', [
                    'error' => 'De weersgegevens konden niet opgehaald worden en er zijn geen opgeslagen gegevens beschikbaar.',
                    'location' => $location,
                    'weekRain' => null,
                    'riskLevel' => null,
                    'weekForecast' => [],
                    'periodLabel' => null,
                    'highestRainDay' => null,
                    'highestRainDayLabel' => null,
                    'role' => $user->role,
                    'fromCache' => false,
                ]);
            }
/** Markeer dat de getoonde data uit de cache komt */
            $fromCache = true;
        }
/** haal de datums en regenval op */
        $dates = $data['daily']['time'] ?? [];
        $rain = $data['daily']['precipitation_sum'] ?? [];

        $weekRain = array_sum($rain);
        $riskLevel = $this->determineRiskLevel($weekRain);

        $periodLabel = null;
// formatteer de totale periode van de getoonde gegevens voor de gebruikersinterface (bv. "01/09 t.e.m. 07/09")
        if (count($dates) > 0) {
            $periodLabel =
                Carbon::parse($dates[0])->locale('nl')->translatedFormat('d/m')
                . ' t.e.m. ' .
                Carbon::parse($dates[count($dates) - 1])->locale('nl')->translatedFormat('d/m');
        }

        $highestRainDay = 0;
        $highestRainDayLabel = null;
/** bepaal de dag met de meeste regen */
        if (count($rain) > 0) {
            $highestRainDay = max($rain);
            $highestRainIndex = array_search($highestRainDay, $rain);
            $highestRainDate = $dates[$highestRainIndex] ?? null;
/** formatteer de datum voor de dag met de meeste regen */
            if ($highestRainDate) {
                $highestRainDayLabel = ucfirst(
                    Carbon::parse($highestRainDate)->locale('nl')->translatedFormat('l d/m')
                );
            }
        }

        $weekForecast = [];
/** bouw een gestructureerde array op voor de dagelijkse voorspellingen */
        foreach ($dates as $index => $date) {
            $dailyRain = $rain[$index] ?? 0;

            $weekForecast[] = [
                'date' => $date,
                'dayLabel' => ucfirst(Carbon::parse($date)->locale('nl')->translatedFormat('l d/m')),
                'rain' => round($dailyRain, 1),
                'riskLevel' => $this->determineDailyRiskLevel($dailyRain),
            ];
        }
/** geef alle berekende data en eventuele waarschuwingen */
        return view('flood-risk.index', [
            'error' => $fromCache ? 'Live weersgegevens zijn tijdelijk niet beschikbaar. We tonen de laatst opgeslagen gegevens.' : null,
            'location' => $location,
            'weekRain' => round($weekRain, 1),
            'riskLevel' => $riskLevel,
            'weekForecast' => $weekForecast,
            'periodLabel' => $periodLabel,
            'highestRainDay' => round($highestRainDay, 1),
            'highestRainDayLabel' => $highestRainDayLabel,
            'role' => $user->role,
            'fromCache' => $fromCache,
        ]);
    }
/** bepaal het algemeen overstromingsrisico  */
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
/** bepaal het dagelijkse overstromingsrisico op basis van de regenval */
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
}
