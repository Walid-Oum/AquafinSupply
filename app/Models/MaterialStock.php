<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * MaterialStock Model
 * 
 * Vertegenwoordigt de voorraad van een materiaal op een specifieke locatie (depot).
 * Elke locatie heeft zijn eigen voorraad en minimum voorraad voor elk materiaal.
 *
 * @author 
 * @version 1.0
 */
class MaterialStock extends Model
{
    /**
     * Velden die massaal mogen worden ingevuld.
     */
    protected $fillable = [
        'material_id',
        'location_id',
        'stock',
        'minimum_stock',
    ];

    /**
     * Relatie: deze voorraad hoort bij een materiaal.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Relatie: deze voorraad hoort bij een locatie (depot).
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}