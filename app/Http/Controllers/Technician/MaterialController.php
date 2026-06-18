<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Support\FuzzySearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MaterialController extends Controller
{
    // Toon lijst van materialen met zoek- en filtermogelijkheden
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
// geef de data door aan de blade view voor weergave
        return view('technician.materials.index', [
            'materials' => $materials,
            'categories' => $categories,
            'recommendedMaterials' => $recommendedMaterials,
            'riskLevel' => $riskLevel,
        ]);
    }
// Toon detailpagina van een specifiek materiaal
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
// Bepaal het overstromingsrisiconiveau op basis van de locatie van de gebruiker
    private function calculateFloodRiskLevel($location): string
    {
        // Als er geen locatie beschikbaar is, wordt het risiconiveau standaard op 'Laag' gezet
        if (! $location) {
            return 'Laag';
        }

        try {
            // Maak een API-aanroep naar Open-Meteo om de neerslaggegevens van de afgelopen week op te halen voor de locatie van de gebruiker
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
            // Bereken de totale neerslag van de afgelopen week door de dagelijkse neerslagwaarden op te tellen

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
