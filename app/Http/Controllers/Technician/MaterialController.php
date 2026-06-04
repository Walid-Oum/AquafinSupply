<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

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
        $categories = Material::select('category')->distinct()->pluck('category');
        
        return view('technician.materials.index', compact('materials', 'categories'));
    }

    public function show($id)
    {
        $material = Material::findOrFail($id);
        return view('technician.materials.show', compact('material'));
    }
}