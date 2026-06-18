<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Material;
use App\Models\MaterialStock;
use App\Models\RiskLevel;
use App\Support\FuzzySearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::with(['stocks', 'riskLevels']);

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereHas('stocks', function ($q) {
                    $q->whereColumn('stock', '<=', 'minimum_stock');
                });
            } elseif ($request->stock_status === 'ok') {
                $query->whereDoesntHave('stocks', function ($q) {
                    $q->whereColumn('stock', '<=', 'minimum_stock');
                });
            }
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $materials = $query
            ->orderBy('name')
            ->get();

        if ($request->filled('search')) {
            $search = $request->search;

            $materials = $materials
                ->filter(function (Material $material) use ($search) {
                    return FuzzySearch::matches($search, $this->materialSearchText($material));
                })
                ->values();
        }

        $lowStockMaterials = Material::with('stocks')
            ->get()
            ->filter(function (Material $material) {
                return $material->stocks->contains(function ($stock) {
                    return $stock->stock <= $stock->minimum_stock;
                });
            });

        $categories = Material::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('materials.index', [
            'materials' => $materials,
            'categories' => $categories,
            'lowStockMaterials' => $lowStockMaterials,
        ]);
    }

    public function create()
    {
        $categories = Material::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $riskLevels = RiskLevel::all();

        return view('materials.create', [
            'categories' => $categories,
            'riskLevels' => $riskLevels,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'risk_levels' => 'nullable|array',
            'risk_levels.*' => 'exists:risk_levels,id',
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
            'stock' => 0,
            'minimum_stock' => 0,
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
                    'stock' => 0,
                    'minimum_stock' => 0,
                ]
            );
        }

        $material->riskLevels()->sync(
            $request->risk_levels ?? []
        );

        return redirect()
            ->route('materials.index')
            ->with('success', 'Materiaal toegevoegd!');
    }

    public function show($id)
    {
        if (auth()->user()->role === 'magazijn') {
            $locationId = auth()->user()->location_id;

            $material = Material::with(['stocks' => function ($query) use ($locationId) {
                $query->where('location_id', $locationId);
            }])->findOrFail($id);

            return view('magazijn.materials.show', [
                'material' => $material,
            ]);
        }

        $material = Material::with(['stocks.location', 'riskLevels'])
            ->findOrFail($id);

        return view('materials.show', [
            'material' => $material,
        ]);
    }

    public function edit($id)
    {
        $material = Material::with('riskLevels')
            ->findOrFail($id);

        $categories = Material::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $riskLevels = RiskLevel::all();

        return view('materials.edit', [
            'material' => $material,
            'categories' => $categories,
            'riskLevels' => $riskLevels,
        ]);
    }

    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        if ($request->remove_image == 1) {
            if ($material->image) {
                Storage::disk('public')->delete($material->image);

                $material->update([
                    'image' => null,
                ]);
            }

            return redirect()
                ->route('materials.edit', $material->id)
                ->with('success', 'Afbeelding verwijderd!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'risk_levels' => 'nullable|array',
            'risk_levels.*' => 'exists:risk_levels,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($material->image) {
                Storage::disk('public')->delete($material->image);
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

        $material->riskLevels()->sync(
            $request->risk_levels ?? []
        );

        return redirect()
            ->route('materials.index')
            ->with('success', 'Materiaal bijgewerkt!');
    }

    public function destroy($id)
    {
        $material = Material::findOrFail($id);

        if ($material->image) {
            Storage::disk('public')->delete($material->image);
        }

        $material->delete();

        return redirect()
            ->route('materials.index')
            ->with('success', 'Materiaal verwijderd!');
    }

    public function warehouseIndex(Request $request)
    {
        $locationId = auth()->user()->location_id;

       $query = Material::where('is_active', true)
    ->with(['stocks' => function ($query) use ($locationId) {
        $query->where('location_id', $locationId);
    }]);if ($request->filled('category')) {
    $query->where('category', $request->category);
}$materials = $query
    ->orderBy('name')
    ->get();

    if ($request->filled('stock_status')) {

    if ($request->stock_status === 'low') {

        $materials = $materials->filter(function ($material) {

            $stock = $material->stocks->first();

            return $stock && $stock->stock <= $stock->minimum_stock;
        });

    }

    if ($request->stock_status === 'ok') {

        $materials = $materials->filter(function ($material) {

            $stock = $material->stocks->first();

            return $stock && $stock->stock > $stock->minimum_stock;
        });

    }

}

        if ($request->filled('search')) {
            $search = $request->search;

            $materials = $materials
                ->filter(function (Material $material) use ($search) {
                    return FuzzySearch::matches($search, $this->materialSearchText($material));
                })
                ->values();
        }

        return view('magazijn.materials.index', [
            'materials' => $materials,
            'categories' => Material::select('category')
    ->distinct()
    ->orderBy('category')
    ->pluck('category'),
        ]);
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
            ->with('success', 'Voorraad bijgewerkt voor jouw depot.');
    }

    public function searchSuggestions(Request $request)
    {
        $search = trim((string) $request->get('q', ''));

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $user = auth()->user();

        $materials = Material::where('is_active', true)
            ->with('stocks')
            ->get()
            ->map(function (Material $material) use ($search) {
                return [
                    'material' => $material,
                    'score' => $this->materialSuggestionScore($search, $material),
                ];
            })
            ->filter(function (array $item) {
                return $item['score'] !== null;
            })
            ->sortBy(function (array $item) {
                return sprintf('%02d-%s', $item['score'], $item['material']->name);
            })
            ->take(5)
            ->map(function (array $item) use ($user) {
                $material = $item['material'];

                if ($user->role === 'admin') {
                    $stock = $material->stocks->sum('stock');
                } else {
                    $localStock = $material->stocks
                        ->where('location_id', $user->location_id)
                        ->first();

                    $stock = $localStock?->stock ?? 0;
                }

                return [
                    'label' => $material->name,
                    'subtitle' => $material->category,
                    'badge' => 'Stock: ' . $stock,
                    'url' => $this->materialSuggestionUrl($material),
                ];
            })
            ->values();

        return response()->json($materials);
    }

    private function materialSuggestionUrl(Material $material): string
    {
        $role = auth()->user()->role;

        if ($role === 'technieker') {
            return route('technician.materials.show', $material->id);
        }

        if ($role === 'magazijn') {
            return route('magazijn.materials.show', $material->id);
        }

        return route('materials.show', $material->id);
    }

    private function materialSearchText(Material $material): string
    {
        $stockStatuses = $material->stocks
            ->map(function ($stock) {
                if ($stock->stock <= 0) {
                    return 'geen voorraad';
                }

                if ($stock->stock <= $stock->minimum_stock) {
                    return 'lage voorraad';
                }

                return 'beschikbaar';
            })
            ->unique()
            ->implode(' ');

        return collect([
            $material->name,
            $material->category,
            $material->description,
            $stockStatuses,
        ])->filter()->implode(' ');
    }

    private function materialSuggestionScore(string $search, Material $material): ?int
    {
        $search = $this->normalizeSearchValue($search);
        $name = $this->normalizeSearchValue($material->name);
        $category = $this->normalizeSearchValue($material->category);

        if ($search === '') {
            return null;
        }

        if ($name === $search) {
            return 0;
        }

        if (str_contains($name, $search)) {
            return 1;
        }

        foreach (explode(' ', $name) as $word) {
            if (str_starts_with($word, $search)) {
                return 2;
            }
        }

        if ($this->isCloseMaterialNameMatch($search, $name)) {
            return 3;
        }

        if (str_contains($category, $search)) {
            return 4;
        }

        return null;
    }

    private function isCloseMaterialNameMatch(string $search, string $name): bool
    {
        if (strlen($search) < 4) {
            return false;
        }

        $allowedDistance = $this->allowedMaterialDistance($search);

        $words = collect(explode(' ', $name))
            ->push(str_replace(' ', '', $name))
            ->filter()
            ->unique();

        foreach ($words as $word) {
            if (levenshtein($search, $word) <= $allowedDistance) {
                return true;
            }

            $searchLength = strlen($search);
            $wordLength = strlen($word);

            $minWindowLength = $searchLength;
            $maxWindowLength = min($wordLength, $searchLength + 1);

            for ($windowLength = $minWindowLength; $windowLength <= $maxWindowLength; $windowLength++) {
                for ($start = 0; $start <= $wordLength - $windowLength; $start++) {
                    $part = substr($word, $start, $windowLength);

                    if (levenshtein($search, $part) <= $allowedDistance) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function allowedMaterialDistance(string $word): int
    {
        $length = strlen($word);

        if ($length <= 5) {
            return 1;
        }

        if ($length <= 9) {
            return 2;
        }

        return 3;
    }

    private function normalizeSearchValue(?string $value): string
    {
        $value = mb_strtolower($value ?? '');

        $value = strtr($value, [
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ä' => 'a',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'ö' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'u',
        ]);

        $value = preg_replace('/[^a-z0-9]+/', ' ', $value);

        return trim($value ?? '');
    }
}
