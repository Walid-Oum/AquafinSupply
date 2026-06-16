<?php

namespace App\Models;
use App\Models\Material;
use Illuminate\Database\Eloquent\Model;

class RiskLevel extends Model
{
    protected $fillable = [
        'name',
    ];
    public function materials()
    {
        return $this->belongsToMany(Material::class);
    }
}

