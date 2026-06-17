<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Support\FuzzySearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $location = $user->location;
        $locationId = $user->location_id;

        $sortDirection = $request->sort === 'desc' ? 'desc' : 'asc';

        $materials = Material::where('is_active', true)
            ->with(['stocks' => function ($query) use ($locationId) {
                $query->where('location_id', $locationId);
            }])
            ->orderBy('name', $sortDirection)
            ->get();

        if ($request->filled('search')) {
            $search = $request->search;

            $materials = $materials
                ->filter(function (Material $material) use ($search) {
                    $localStock = $material->stocks->first();
                    $stock = $localStock?->stock ?? 0;
                    $minimumStock = $localStock?->minimum_stock ?? 0;

                    if ($stock <= 0) {
                        $stockStatus = 'geen voorraad';
                    } elseif ($stock <= $minimumStock) {
                        $stockStatus = 'lage voorraad';
                    } else {
                        $stockStatus = 'beschikbaar';
                    }

                    $searchableText = collect([
                        $material->name,
                        $material->category,
                        $stock,
                        $stockStatus,
                    ])->filter()->implode(' ');

                    return FuzzySearch::matches($search, $searchableText);
                })
                ->values();
        }

        $categories = Material::where('is_active', true)
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $riskLevel = $this->calculateFloodRiskLevel($location);

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

        return view('technician.materials.index', [
            'materials' => $materials,
            'categories' => $categories,
            'recommendedMaterials' => $recommendedMaterials,
            'riskLevel' => $riskLevel,
        ]);
    }

    public function show($id)
    {
        $locationId = auth()->user()->location_id;

        $material = Material::with(['stocks' => function ($query) use ($locationId) {
            $query->where('location_id', $locationId);
        }])->findOrFail($id);

        return view('technician.materials.show', [
            'material' => $material,
        ]);
    }

    private function calculateFloodRiskLevel($location): string
    {
        if (! $location) {
            return 'Laag';
        }

        try {
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
