<?php

namespace App\Models;

use App\Models\Material;
use Illuminate\Database\Eloquent\Model;

/**
 * RiskLevel Model
 * 
 * Vertegenwoordigt een risiconiveau (bv. Laag, Gemiddeld, Hoog).
 * Wordt gebruikt om materialen te koppelen aan overstromingsrisico's.
 *
 * @author 
 * @version 1.0
 */
class RiskLevel extends Model
{
    /**
     * Velden die massaal mogen worden ingevuld.
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Relatie: dit risiconiveau kan aan meerdere materialen gekoppeld zijn.
     */
    public function materials()
    {
        return $this->belongsToMany(Material::class);
    }
}