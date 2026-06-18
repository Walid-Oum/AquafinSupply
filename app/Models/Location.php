<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Location Model
 * 
 * Vertegenwoordigt een depot/vestiging. Gebruikers en voorraden zijn gekoppeld aan een locatie.
 *
 * @author 
 * @version 1.0
 */
class Location extends Model
{
    /**
     * Velden die massaal mogen worden ingevuld.
     */
    protected $fillable = [
        'name',
        'city',
        'postal_code',
        'latitude',
        'longitude',
        'depot_address',
        'province',
    ];

    /**
     * Relatie: gebruikers die bij deze locatie horen.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relatie: voorraden van materialen op deze locatie.
     */
    public function materialStocks()
    {
        return $this->hasMany(MaterialStock::class);
    }
}