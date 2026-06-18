<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Support\FuzzySearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Controller voor techniekers om materialen te bekijken.
 *
 * Deze controller beheert de materiaalflow van de technieker.
 * Techniekers kunnen actieve materialen bekijken, zoeken, filteren,
 * sorteren en detailinformatie raadplegen.
 *
 * Daarnaast worden aanbevolen materialen getoond op basis van het
 * berekende overstromingsrisico voor de locatie van de technieker.
 *
 * Functionaliteiten:
 * - Materialenoverzicht tonen
 * - Materialen zoeken via fuzzy search
 * - Materialen filteren op categorie
 * - Materiaaldetails bekijken
 * - Aanbevolen materialen tonen op basis van overstromingsrisico
 */
class MaterialController extends Controller
{
    /**
     * Toon een overzicht van alle actieve materialen.
     *
     * Deze methode haalt alle actieve materialen op voor de ingelogde
     * technieker en toont daarbij enkel de voorraad van het depot
     * waaraan de technieker gekoppeld is.
     *
     * Ondersteunt:
     * - Sorteren op naam
     * - Zoeken via fuzzy search
     * - Filteren op categorie
     * - Aanbevolen materialen op basis van overstromingsrisico
     *
     * @param Request $request De HTTP-request met optionele zoek- en sorteerparameters.
     * @return \Illuminate\View\View De view met materialen, categorieën en aanbevelingen.
     */
    public function index(Request $request)
    {
        // Haal huidige gebruiker en locatie op
        $user = auth()->user();
        $location = $user->location;
        $locationId = $user->location_id;

        $sortDirection = $request->sort === 'desc' ? 'desc' : 'asc';

        // Haal actieve materialen op met voorraad voor huidige locatie
        $materials = Material::where('is_active', true)
            ->with(['stocks' => function ($query) use ($locationId) {
                $query->where('location_id', $locationId);
            }])
            ->orderBy('name', $sortDirection)
            ->get();

        // Pas fuzzy zoekfilter toe als er een zoekterm is
        if ($request->filled('search')) {
            $search = $request->search;

            $materials = $materials
                ->filter(function (Material $material) use ($search) {
                    // Bepaal voorraadstatus voor huidige locatie
                    $localStock = $material->stocks->first();
                    $stock = $localStock?->stock ?? 0;
                    $minimumStock = $localStock?->minimum_stock ?? 0;

                    // Bepaal voorraadstatus op basis van stock en minimum_stock
                    if ($stock <= 0) {
                        $stockStatus = 'geen voorraad';
                    } elseif ($stock <= $minimumStock) {
                        $stockStatus = 'lage voorraad';
                    } else {
                        $stockStatus = 'beschikbaar';
                    }

                    // Combineer relevante tekstvelden tot één string voor fuzzy matching
                    $searchableText = collect([
                        $material->name,
                        $material->category,
                        $stock,
                        $stockStatus,
                    ])->filter()->implode(' ');

                    // Gebruik FuzzySearch om te bepalen of de zoekterm overeenkomt met de samengestelde tekst
                    return FuzzySearch::matches($search, $searchableText);
                })
                ->values();
        }

        // Haal unieke categorieën op voor filter dropdown
        $categories = Material::where('is_active', true)
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        // Bepaal het overstromingsrisiconiveau op basis van de locatie van de gebruiker
        $riskLevel = $this->calculateFloodRiskLevel($location);

        // Haal aanbevolen materialen op die overeenkomen met het risiconiveau en beschikbaar zijn in de huidige locatie
        $recommendedMaterials = Material::where('is_active', true)
            ->whereHas('riskLevels', function ($query) use ($riskLevel) {
                $query->where('name', $riskLevel);
            })
            ->with(['stocks' => function ($query) use ($locationId) {
                $query->where('location_id', $locationId);
            }])
            ->inRandomOrder()
            ->take(8)
            ->get();

        // Geef de data door aan de Blade-view voor weergave
        return view('technician.materials.index', [
            'materials' => $materials,
            'categories' => $categories,
            'recommendedMaterials' => $recommendedMaterials,
            'riskLevel' => $riskLevel,
        ]);
    }

    /**
     * Toon de detailpagina van een specifiek materiaal.
     *
     * Enkel de voorraad van het depot van de ingelogde technieker
     * wordt weergegeven.
     *
     * @param int|string $id Het ID van het materiaal.
     * @return \Illuminate\View\View De detailpagina van het materiaal.
     */
    public function show($id)
    {
        $locationId = auth()->user()->location_id;

        // Haal het materiaal op met voorraad voor de huidige locatie
        $material = Material::with(['stocks' => function ($query) use ($locationId) {
            $query->where('location_id', $locationId);
        }])->findOrFail($id);

        return view('technician.materials.show', [
            'material' => $material,
        ]);
    }

    /**
     * Bepaal het overstromingsrisico voor de locatie
     * van de technieker op basis van weersgegevens
     * afkomstig van de Open-Meteo API.
     *
     * De methode haalt de verwachte neerslag voor de komende
     * 7 dagen op en telt deze waarden op. Op basis van de totale
     * verwachte neerslag wordt een risiconiveau teruggegeven.
     *
     * Mogelijke waarden:
     * - Laag
     * - Gemiddeld
     * - Hoog
     *
     * @param mixed $location De locatie van de ingelogde technieker.
     * @return string Het berekende risiconiveau.
     */
    private function calculateFloodRiskLevel($location): string
    {
        // Als er geen locatie beschikbaar is, wordt het risiconiveau standaard op 'Laag' gezet
        if (! $location) {
            return 'Laag';
        }

        try {
            // Maak een API-aanroep naar Open-Meteo om de verwachte neerslag voor de komende 7 dagen op te halen
            $response = Http::timeout(5)->get(
                'https://api.open-meteo.com/v1/forecast',
                [
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'daily' => 'precipitation_sum',
                    'timezone' => 'Europe/Brussels',
                    'forecast_days' => 7,
                ]
            );

            if (! $response->successful()) {
                return 'Laag';
            }

            $data = $response->json();

            // Bereken de totale verwachte neerslag voor de komende 7 dagen
            $weekRain = array_sum($data['daily']['precipitation_sum'] ?? []);

            if ($weekRain < 20) {
                return 'Laag';
            }

            if ($weekRain < 50) {
                return 'Gemiddeld';
            }

            return 'Hoog';
        } catch (\Exception $exception) {
            return 'Laag';
        }
    }
}
