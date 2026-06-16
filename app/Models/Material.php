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

    public function orderItems()
{
    return $this->hasMany(OrderItem::class);
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

