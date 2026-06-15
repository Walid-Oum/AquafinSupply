<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class FloodRiskController extends Controller
{
    // Deze controller beheert de weerrisico's voor overstromingen op basis van de locaties van de depots.
    public function index()
    {
        // Haal alle locaties op, gesorteerd op provincie
        $locations = Location::orderBy('province')->get();

        $provinceStats = [];
// Voor elke locatie worden de weersgegevens opgehaald en de risico's berekend
        foreach ($locations as $location) {
            $stats = $this->buildLocationWeatherStats($location);
// Alleen locaties waarvoor de weersgegevens succesvol zijn opgehaald, worden toegevoegd aan de statistieken
            if ($stats !== null) {
                $provinceStats[] = $stats;
            }
        }
// De index view wordt geretourneerd met de locaties en hun bijbehorende statistieken
        return view('admin.flood-risk.index', [
            'locations' => $locations,
            'provinceStats' => $provinceStats,
        ]);
    }
// De show methode toont de gedetailleerde weerrisico's voor een specifieke locatie
    public function show(Location $location)
    {
        // Bereken de weersstatistieken voor de geselecteerde locatie
        $selectedStats = $this->buildLocationWeatherStats($location);
      // Als de Api-aanroep mislukt en er geen statistieken kunnen worden opgehaald, wordt de gebruiker teruggeleid naar de index met een foutmelding
        if ($selectedStats === null) {
            return redirect()
                ->route('admin.flood-risk.index')
                ->with('error', 'De weersgegevens konden niet opgehaald worden.');
        }
// De show view wordt geretourneerd met de locatie en de bijbehorende statistieken
        return view('admin.flood-risk.show', [
            'location' => $location,
            'selectedStats' => $selectedStats,
        ]);
    }
// Deze methoden bepalen het risico op overstromingen op basis van de hoeveelheid neerslag
    private function determineRiskLevel(float $rainfall): string
    {
        // Minder dan 20mm neerslag in de komende week wordt als laag risico beschouwd, tussen 20mm en 50mm als gemiddeld, en meer dan 50mm als hoog risico
        if ($rainfall < 20) {
            return 'Laag';
        }
// Tussen 20mm en 50mm neerslag in de komende week wordt als gemiddeld risico beschouwd
        if ($rainfall < 50) {
            return 'Gemiddeld';
        }
// Meer dan 50mm neerslag in de komende week wordt als hoog risico beschouwd
        return 'Hoog';
    }
// bepaal het dagelijkse risico op overstromingen op basis van de hoeveelheid neerslag op die dag
    private function determineDailyRiskLevel(float $rainfall): string
    {
        // Minder dan 5mm neerslag op een dag wordt als laag risico beschouwd, tussen 5mm en 15mm als gemiddeld, en meer dan 15mm als hoog risico
        if ($rainfall < 5) {
            return 'Laag';
        }
// Tussen 5mm en 15mm neerslag op een dag wordt als gemiddeld risico beschouwd
        if ($rainfall < 15) {
            return 'Gemiddeld';
        }
// Meer dan 15mm neerslag op een dag wordt als hoog risico beschouwd
        return 'Hoog';
    }
// Deze methode haalt de weersgegevens op voor een specifieke locatie en berekent de statistieken die nodig zijn om het risico op overstromingen te beoordelen
    private function buildLocationWeatherStats(Location $location): ?array
    {
        // start een https verzoek naar de Open-Meteo API om de weersvoorspelling op te halen voor de opgegeven locatie
        $response = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_max,rain_sum,wind_gusts_10m_max',
            'timezone' => 'Europe/Brussels',
            'forecast_days' => 16,
        ]);
// faalt de API-aanroep, retourneer dan null om aan te geven dat de gegevens niet kunnen worden opgehaald
        if ($response->failed()) {
            return null;
        }
// converteer de JSON-respons naar een array en haal de relevante gegevens op voor de komende week
        $data = $response->json();
// Haal de datums en neerslaggegevens op uit de respons
        $dates = $data['daily']['time'];
        $rain = $data['daily']['precipitation_sum'];
// Bereken de totale neerslag voor de huidige week en de volgende week
        $currentWeekRain = array_sum(array_slice($rain, 0, 7));
        $nextWeekRain = array_sum(array_slice($rain, 7, 7));
// snijd de specifieke datums en neerslaggegevens voor de komende week uit de arrays
        $nextWeekDates = array_slice($dates, 7, 7);
        $nextWeekDailyRain = array_slice($rain, 7, 7);
// pak de start- en einddatum van de komende week op basis van de opgehaalde datums
        $nextWeekStartDate = $nextWeekDates[0] ?? null;
        $nextWeekEndDate = end($nextWeekDates) ?: null;

        $nextWeekPeriodLabel = null;
// formateer de start- en einddatum van de komende week naar een leesbaar formaat in het Nederlands, bijvoorbeeld "01/01 t.e.m. 07/01"
        if ($nextWeekStartDate && $nextWeekEndDate) {
            $nextWeekPeriodLabel =
                Carbon::parse($nextWeekStartDate)->locale('nl')->translatedFormat('d/m')
                . ' t.e.m. ' .
                Carbon::parse($nextWeekEndDate)->locale('nl')->translatedFormat('d/m');
        }

        $highestRainDay = 0;
        $highestRainDate = null;
        $highestRainDayLabel = null;
// zoek de dag met de hoogste neerslag in de komende week en formatteer de datum naar een leesbaar formaat in het Nederlands
        if (count($nextWeekDailyRain) > 0) {
            $highestRainDay = max($nextWeekDailyRain);
            $highestRainIndex = array_search($highestRainDay, $nextWeekDailyRain);
            $highestRainDate = $nextWeekDates[$highestRainIndex] ?? null;
// formatteer de datum van de dag met de hoogste neerslag naar een leesbaar formaat in het Nederlands, bijvoorbeeld "Maandag 01/01"
            if ($highestRainDate) {
                $highestRainDayLabel = ucfirst(
                    Carbon::parse($highestRainDate)->locale('nl')->translatedFormat('l d/m')
                );
            }
        }
// Tel het aantal dagen in de komende week met neerslag van 15mm of meer om het risico op overstromingen te beoordelen
        $riskDays = collect($nextWeekDailyRain)
            ->filter(fn ($dailyRain) => $dailyRain >= 15)
            ->count();
// bepaal het algemene risico op overstromingen voor de komende week op basis van de totale neerslag
        $riskLevel = $this->determineRiskLevel($nextWeekRain);

        $nextWeekForecast = [];
// bouw een overzicht van de weersvoorspelling voor de komende week, inclusief de datum, neerslag en het risico op overstromingen
        foreach ($nextWeekDates as $index => $date) {
            $dailyRain = $nextWeekDailyRain[$index] ?? 0;

            $nextWeekForecast[] = [
                'date' => $date,
                // zet datum om naar bijvoorbeeld "Maandag 01/01" formaat in het Nederlands
                'dayLabel' => ucfirst(Carbon::parse($date)->locale('nl')->translatedFormat('l d/m')),
                'rain' => round($dailyRain, 1),
                'riskLevel' => $this->determineDailyRiskLevel($dailyRain),
            ];
        }
// Geef de locatiegegevens en de berekende statistieken terug als een array
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
