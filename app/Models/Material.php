<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'name',
        'category',
        'description',
        'stock',
        'minimum_stock',
        'is_active',
        'image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'stock' => 'integer',
        'minimum_stock' => 'integer',
    ];

    // Relatie met OrderItem: een materiaal kan in meerdere orderitems voorkomen
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

   // slimme zoekfunctie die spaties negeert en prioriteit geeft aan exacte matches of items die beginnen met de zoekterm
    public function scopeSearch($query, $searchTerm)
    {
        if (!$searchTerm) {
            return $query;
        }

        // Spatieloze zoekterm maken (bijv. van "pvc buis" naar "pvcbuis")
        $cleanSearch = str_replace(' ', '', $searchTerm);

        return $query->where(function ($q) use ($searchTerm, $cleanSearch) {
            $q->whereRaw("REPLACE(name, ' ', '') LIKE ?", ["%{$cleanSearch}%"])
              ->orWhereRaw("REPLACE(description, ' ', '') LIKE ?", ["%{$cleanSearch}%"])
              ->orWhereRaw("REPLACE(category, ' ', '') LIKE ?", ["%{$cleanSearch}%"]);
        })
        
        ->orderByRaw("
            CASE 
                WHEN name LIKE ? THEN 2
                WHEN name LIKE ? THEN 1
                ELSE 0
            END DESC
        ", ["{$searchTerm}", "%{$searchTerm}%"]);
    }
}