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
    ];

    public function user(){
        return $this->hasMany(User::class);

    }

}
