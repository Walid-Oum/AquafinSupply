<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::query();

        // Filter op categorie
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        $materials = $query->get();

        $lowStockMaterials = Material::whereColumn(
            'stock',
            '<=',
            'minimum_stock'
        )->get();

        // Haal alle unieke categorieën op voor de filter dropdown
        $categories = Material::select('category')->distinct()->pluck('category');

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
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('materials', 'public');
        }

        Material::create([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'stock' => $request->stock,
            'is_active' => true,
            'image' => $imagePath,
        ]);

        return redirect()->route('materials.index')->with('success', 'Materiaal toegevoegd!');
    }

    public function show($id)
    {
        $material = Material::findOrFail($id);
        return view('materials.show', compact('material'));
    }

    public function edit($id)
    {
        $material = Material::findOrFail($id);
        return view('materials.edit', compact('material'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'minimum_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $material = Material::findOrFail($id);

        if ($request->hasFile('image')) {
            // Verwijder oude afbeelding als die bestaat
            if ($material->image) {
                Storage::disk('public')->delete($material->image);
            }
            $imagePath = $request->file('image')->store('materials', 'public');
            $material->image = $imagePath;
        }

        $material->update([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'stock' => $request->stock,
            'is_active' => $request->is_active,
            'minimum_stock' => $request->minimum_stock,
        ]);

        return redirect()->route('materials.index')->with('success', 'Materiaal bijgewerkt!');
    }

    public function destroy($id)
    {
        $material = Material::findOrFail($id);
        
        // Verwijder de afbeelding als die bestaat
        if ($material->image) 
            Storage::disk('public')->delete($material->image);
        }
        
        $material->delete();

        return redirect()->route('materials.index')->with('success', 'Materiaal verwijderd!');
    }
}   