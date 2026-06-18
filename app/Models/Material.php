<?php

namespace App\Models;

use App\Models\RiskLevel;
use Illuminate\Database\Eloquent\Model;

/**
 * Material Model
 * 
 * Vertegenwoordigt een materiaal in de applicatie. Bevat alle eigenschappen
 * zoals naam, categorie, voorraad en afbeelding. Heeft relaties met
 * orderitems, voorraden en risiconiveaus.
 *
 * @author
 * @version 1.0
 */
class Material extends Model
{
    /**
     * Velden die massaal mogen worden ingevuld.
     */
    protected $fillable = [
        'name',
        'category',
        'description',
        'stock',
        'minimum_stock',
        'is_active',
        'image',
    ];

    /**
     * Attributen die naar een specifiek type moeten worden omgezet.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'stock' => 'integer',
        'minimum_stock' => 'integer',
    ];

    /**
     * Relatie: een materiaal kan in meerdere orderitems voorkomen.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Basis scope voor zoeken.
     * De échte fouttolerante fuzzy search wordt in de controllers afgehandeld.
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query;
    }

    /**
     * Relatie: voorraden van dit materiaal per locatie.
     */
    public function stocks()
    {
        return $this->hasMany(MaterialStock::class);
    }

    /**
     * Relatie: risiconiveaus die aan dit materiaal zijn gekoppeld.
     */
    public function riskLevels()
    {
        return $this->belongsToMany(RiskLevel::class);
    }
}