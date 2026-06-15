<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $location = $user->location;
        $locationId = $user->location_id;

        $query = Material::where('is_active', true)
            ->with(['stocks' => function ($query) use ($locationId) {
                $query->where('location_id', $locationId);
            }]);

        // Zoeken
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter op categorie
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Sorteren
        if ($request->sort === 'desc') {
            $query->orderBy('name', 'desc');
        } else {
            $query->orderBy('name', 'asc');
        }

        $materials = $query->get();

        $categories = Material::where('is_active', true)
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $riskLevel = 'Laag';

        if ($location) {
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

                if ($response->successful()) {
                    $data = $response->json();

                    $weekRain = array_sum($data['daily']['precipitation_sum'] ?? []);

                    if ($weekRain < 20) {
                        $riskLevel = 'Laag';
                    } elseif ($weekRain < 50) {
                        $riskLevel = 'Gemiddeld';
                    } else {
                        $riskLevel = 'Hoog';
                    }
                }
            } catch (\Exception $exception) {
                $riskLevel = 'Laag';
            }
        }

        if ($riskLevel === 'Hoog') {
            $recommendedNames = [
                'Dompelpomp',
                'Rioolstop',
                'Werklaarzen PVC',
                'Slangenwagen',
            ];
        } elseif ($riskLevel === 'Gemiddeld') {
            $recommendedNames = [
                'Regenjas',
                'Slangenwagen',
                'Werklaarzen PVC',
                'Fluovest',
            ];
        } else {
            $recommendedNames = [
                'Gasdetectiemeter',
                'EHBO-kit',
                'Fluovest',
                'Veiligheidshelm',
            ];
        }

        $recommendedMaterials = Material::where('is_active', true)
            ->whereIn('name', $recommendedNames)
            ->with(['stocks' => function ($query) use ($locationId) {
                $query->where('location_id', $locationId);
            }])
            ->get();

        return view(
            'technician.materials.index',
            compact(
                'materials',
                'categories',
                'recommendedMaterials',
                'riskLevel'
            )
        );
    }

    public function show($id)
    {
        $locationId = auth()->user()->location_id;

        $material = Material::with(['stocks' => function ($query) use ($locationId) {
            $query->where('location_id', $locationId);
        }])->findOrFail($id);

        return view('technician.materials.show', compact('material'));
    }
}
