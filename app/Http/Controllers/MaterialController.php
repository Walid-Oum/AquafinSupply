<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Material;
use App\Models\MaterialStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::query()
            ->with('stocks');

        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        $materials = $query
            ->orderBy('name')
            ->get();

        $lowStockMaterials = Material::with('stocks')
            ->get()
            ->filter(function ($material) {
                return $material->stocks->contains(function ($stock) {
                    return $stock->stock <= $stock->minimum_stock;
                });
            });

        $categories = Material::select('category')
            ->distinct()
            ->pluck('category');

        return view(
            'materials.index',
            compact(
                'materials',
                'categories',
                'lowStockMaterials'
            )
        );
    }

    public function create()
    {
        return view('materials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request
                ->file('image')
                ->store('materials', 'public');
        }

        $material = Material::create([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'stock' => $request->stock,
            'minimum_stock' => $request->minimum_stock ?? 0,
            'is_active' => true,
            'image' => $imagePath,
        ]);

        foreach (Location::all() as $location) {
            MaterialStock::updateOrCreate(
                [
                    'material_id' => $material->id,
                    'location_id' => $location->id,
                ],
                [
                    'stock' => $request->stock,
                    'minimum_stock' => $request->minimum_stock ?? 0,
                ]
            );
        }

        return redirect()
            ->route('materials.index')
            ->with(
                'success',
                'Materiaal toegevoegd!'
            );
    }

    public function show($id)
    {
        if (auth()->user()->role === 'magazijn') {
            $locationId = auth()->user()->location_id;

            $material = Material::with(['stocks' => function ($query) use ($locationId) {
                $query->where('location_id', $locationId);
            }])->findOrFail($id);

            return view(
                'magazijn.materials.show',
                compact('material')
            );
        }

        $material = Material::with('stocks.location')
            ->findOrFail($id);

        return view(
            'materials.show',
            compact('material')
        );
    }

    public function edit($id)
    {
        $material = Material::findOrFail($id);

        return view(
            'materials.edit',
            compact('material')
        );
    }

    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        // Afbeelding verwijderen (alleen de afbeelding, niet het materiaal)
        if ($request->has('delete_image')) {
            if ($material->image) {
                Storage::disk('public')->delete($material->image);
                $material->image = null;
                $material->save();
            }
            return redirect()
                ->route('materials.edit', $material->id)
                ->with('success', 'Afbeelding verwijderd!');
        }

        // Normale validatie
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

<<<<<<< HEAD
        if ($request->hasFile('image')) {
=======
        $material = Material::findOrFail($id);


        if ($request->remove_image) {
            if ($material->image) {
                Storage::disk('public')
                    ->delete($material->image);
>>>>>>> main

                $material->image = null;
            }
        }

        if ($request->hasFile('image')) {
            if ($material->image) {
                Storage::disk('public')
                    ->delete($material->image);
            }

            $material->image = $request
                ->file('image')
                ->store('materials', 'public');
        }

        $material->update([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'is_active' => $request->is_active,
            'image' => $material->image,
        ]);

        return redirect()
            ->route('materials.index')
            ->with(
                'success',
                'Materiaal bijgewerkt!'
            );
    }

    public function destroy($id)
    {
        $material = Material::findOrFail($id);

        if ($material->image) {
            Storage::disk('public')
                ->delete($material->image);
        }

        $material->delete();

        return redirect()
            ->route('materials.index')
            ->with(
                'success',
                'Materiaal verwijderd!'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | MAGAZIJN
    |--------------------------------------------------------------------------
    */

    public function warehouseIndex(Request $request)
    {
        $locationId = auth()->user()->location_id;

        $query = Material::where('is_active', true)
            ->with(['stocks' => function ($query) use ($locationId) {
                $query->where('location_id', $locationId);
            }]);

        if ($request->search) {
            $query->where(
                'name',
                'like',
                '%' . $request->search . '%'
            );
        }

        $materials = $query
            ->orderBy('name')
            ->get();

        return view(
            'magazijn.materials.index',
            compact('materials')
        );
    }

    public function warehouseUpdate(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
        ]);

        $material = Material::findOrFail($id);

        $materialStock = MaterialStock::firstOrCreate(
            [
                'material_id' => $material->id,
                'location_id' => auth()->user()->location_id,
            ],
            [
                'stock' => 0,
                'minimum_stock' => $material->minimum_stock ?? 0,
            ]
        );

        $materialStock->update([
            'stock' => $request->stock,
            'minimum_stock' => $request->minimum_stock ?? $materialStock->minimum_stock,
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Voorraad bijgewerkt voor jouw depot.'
            );
    }

    public function searchSuggestions(Request $request)
    {
        $search = $request->get('q');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $user = auth()->user();

        $materials = Material::where('name', 'LIKE', "%{$search}%")
            ->where('is_active', true)
            ->with('stocks')
            ->limit(5)
            ->get()
            ->map(function ($material) use ($user) {
                if ($user->role === 'admin') {
                    $stock = $material->stocks->sum('stock');
                } else {
                    $localStock = $material->stocks
                        ->where('location_id', $user->location_id)
                        ->first();

                    $stock = $localStock?->stock ?? 0;
                }

                return [
                    'id' => $material->id,
                    'name' => $material->name,
                    'stock' => $stock,
                ];
            });

        return response()->json($materials);
    }
}
