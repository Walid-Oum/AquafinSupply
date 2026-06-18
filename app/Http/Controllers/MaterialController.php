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
    /** Toon hoofdindex van materialen voor beheerders
     * inclusief complexe database filters op voorraadstatus en categorie
     */
    public function index(Request $request)
    {
        /** start de query en laad direct de gekoppelde voorraden en risiconiveaus */
        $query = Material::with(['stocks', 'riskLevels']);
/** filteren op vooraadstatus */
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                /** alleen materialen waarvan de voorraad kleiner of gelijk is aan het minimum */
                $query->whereHas('stocks', function ($q) {
                    $q->whereColumn('stock', '<=', 'minimum_stock');
                });
            } elseif ($request->stock_status === 'ok') {
                /** alleen materialen die overal voldoende voorraad hebben */
                $query->whereDoesntHave('stocks', function ($q) {
                    $q->whereColumn('stock', '<=', 'minimum_stock');
                });
            }
        }
/** filteren op categorie */
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
/** filteren op zoekterm */
        $materials = $query
            ->orderBy('name')
            ->get();
/** pas fuzzy zoekfilter toe als er een zoekterm is */
        if ($request->filled('search')) {
            $search = $request->search;

            $materials = $materials
                ->filter(function (Material $material) use ($search) {
                    return FuzzySearch::matches($search, $this->materialSearchText($material));
                })
                ->values();
        }
/** haal unieke categorieën op voor filter dropdown */
        $lowStockMaterials = Material::with('stocks')
            ->get()
            ->filter(function (Material $material) {
                return $material->stocks->contains(function ($stock) {
                    return $stock->stock <= $stock->minimum_stock;
                });
            });
/** Haal unieke categorieën op voor filter dropdown */
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
/** Toon het formulier voor het aanmaken van een nieuw materiaal */
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
/** Sla een nieuw materiaal op in de database
 * en genereer voorraadregels voor alle bestaande locaties
*/
    public function store(Request $request)
    {
        /** validatie van de invoergevens */
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'risk_levels' => 'nullable|array',
            'risk_levels.*' => 'exists:risk_levels,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
/** sla de afbeelding op in de public storage onder de materials directory */
        if ($request->hasFile('image')) {
            $imagePath = $request
                ->file('image')
                ->store('materials', 'public');
        }
/** Maak het hoofd-materiaal aan */
        $material = Material::create([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'stock' => 0,
            'minimum_stock' => 0,
            'is_active' => true,
            'image' => $imagePath,
        ]);
/** initialiseer voorraadregels voor alle locaties */
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
/** koppel de geselecteerde risiconiveaus aan het materiaal */
        $material->riskLevels()->sync(
            $request->risk_levels ?? []
        );

        return redirect()
            ->route('materials.index')
            ->with('success', 'Materiaal toegevoegd!');
    }
/** Toon details van een specifiek materiaal */
    public function show($id)
    {
        /** als de gebruiker een magazijnmedewerker is */
        if (auth()->user()->role === 'magazijn') {
            $locationId = auth()->user()->location_id;

            $material = Material::with(['stocks' => function ($query) use ($locationId) {
                $query->where('location_id', $locationId);
            }])->findOrFail($id);

            return view('magazijn.materials.show', [
                'material' => $material,
            ]);
        }
/** voor admin/andere rollen: toon alle locaties */
        $material = Material::with(['stocks.location', 'riskLevels'])
            ->findOrFail($id);

        return view('materials.show', [
            'material' => $material,
        ]);
    }
/** Toon het bewerkingsformulier voor een specifiek materiaal */
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
/** werk de materiaalgegevens bij een handhaaf de opslag van afbeeldingen (vervangen / verwijderen)*/
    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);
/** Specifieke afhandeling voor de losse actie 'afbeelding verwijderen' */
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
/** reguliere validatie */ 
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'risk_levels' => 'nullable|array',
            'risk_levels.*' => 'exists:risk_levels,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
/** indienen een nieuwe afbeelding is geupload, verwijder de oude en sla de nieuwe op  */
        if ($request->hasFile('image')) {
            if ($material->image) {
                Storage::disk('public')->delete($material->image);
            }

            $material->image = $request
                ->file('image')
                ->store('materials', 'public');
        }
/** update de database-velden */
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
/**
     * Verwijder het materiaal volledig, inclusief de fysieke afbeelding van de schijf.
     */
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
/**
     * Indexpagina specifiek voor magazijnmedewerkers.
     * Filtert voorraden direct op de locatie van de ingelogde medewerker.
     */
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
/**
     * Update de voorraadstand of de minimum kritieke grens voor het eigen depot van de magazijnmedewerker.
     */
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
/**
     * API Eindpunt: Levert realtime zoeksuggesties (JSON) op voor een AJAX typeahead/autocomplete component.
     * Sorteert resultaten op basis van relevantie-scores en berekent voorraadaantallen rol-afhankelijk.
     */
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
/**
     * Bepaal de juiste detail-route voor een zoeksuggestie op basis van de rol van de gebruiker.
     */
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
/**
     * Bouw een samengestelde tekststring op met alle kenmerken van een materiaal
     * om een brede in-memory fuzzy search mogelijk te maken.
     */
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
/**
     * Bereken de relevantie-score voor de autocomplete suggesties (hoe lager de score, hoe relevanter).
     * 0 = Exacte naam-match
     * 1 = Zoekterm bevindt zich ergens in de naam
     * 2 = Een van de losse woorden in de naam begint met de zoekterm
     * 3 = Typfout/Fuzzy match op basis van de Levenshtein-afstand
     * 4 = Zoekterm bevindt zich in de categorienaam
     */
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
/**
     * Geavanceerde fuzzy matching logica die controleert of een zoekterm (inclusief typfouten)
     * dicht genoeg bij de materiaalnaam of delen daarvan ligt met behulp van de Levenshtein-afstand.
     */
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
/**
     * Bepaal de maximaal toegestane Levenshtein-afstand (bewerkingsafstand)
     * om de tolerantie voor typefouten dynamisch te schalen met de lengte van het woord.
     */
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

 /**
     * Saniteert en normaliseert invoerstrings ten behoeve van betrouwbare zoekopdrachten.
     * Zet om naar kleine letters, vervangt alle diakritische tekens (accenten) en
     * converteert alle speciale tekens/leestekens naar spaties.
     */
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
