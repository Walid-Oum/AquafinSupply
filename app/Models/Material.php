<?php

namespace App\Models;

use App\Models\RiskLevel;
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

    /**
     * Basis scope voor zoeken.
     * De échte fouttolerante fuzzy search wordt nu in de controllers afgehandeld via FuzzySearch.
     */
    public function scopeSearch($query, $searchTerm)
    {
        // We retourneren simpelweg de query. De filtering gebeurt nu 100% accuraat in de controller.
        return $query;
    }

    public function stocks()
    {
        return $this->hasMany(MaterialStock::class);
    }

    public function riskLevels()
    {
        return $this->belongsToMany(RiskLevel::class);
    }
}
