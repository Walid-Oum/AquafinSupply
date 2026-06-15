<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name',
        'city',
        'postal_code',
        'latitude',
        'longitude',
        'depot_address',
        'province',
    ];

    public function users(){
        return $this->hasMany(User::class);

    }

    public function materialStocks()
    {
        return $this->hasMany(MaterialStock::class);
    }

}
