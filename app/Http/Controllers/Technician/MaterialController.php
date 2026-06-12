<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::where('is_active', true);

        // Zoeken
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter op categorie
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        // Sorteren
        if ($request->has('sort')) {
            if ($request->sort == 'asc') {
                $query->orderBy('name', 'asc');
            } elseif ($request->sort == 'desc') {
                $query->orderBy('name', 'desc');
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        $materials = $query->get();

        $categories = Material::select('category')
            ->distinct()
            ->pluck('category');

       $user = auth()->user();
       $location = $user->location;
       $riskLevel = 'Laag';
        if ($location) {

            $response = Http::get(
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

                $weekRain = array_sum(
                    $data['daily']['precipitation_sum']
                );

                if ($weekRain < 20) {

                    $riskLevel = 'Laag';

                } elseif ($weekRain < 50) {

                    $riskLevel = 'Gemiddeld';

                } else {

                    $riskLevel = 'Hoog';

                }
            }
        }

        if ($riskLevel === 'Hoog') {

            $recommendedMaterials = Material::whereIn('name', [
                'Dompelpomp',
                'Rioolstop',
                'Werklaarzen PVC',
                'Slangenwagen'
            ])->get();

        } elseif ($riskLevel === 'Gemiddeld') {

            $recommendedMaterials = Material::whereIn('name', [
                'Regenjas',
                'Slangenwagen',
                'Werklaarzen PVC',
                'Fluovest'
            ])->get();

        } else {

            $recommendedMaterials = Material::whereIn('name', [
                'Gasdetectiemeter',
                'EHBO-kit',
                'Fluovest',
                'Veiligheidshelm'
            ])->get();

        }

        return view(
            'technician.materials.index',
            compact(
                'materials',
                'categories',
                'recommendedMaterials',
                'riskLevel',
            )
        );
    }

    public function show($id)
    {
        $material = Material::findOrFail($id);
        return view('technician.materials.show', compact('material'));
    }
}
