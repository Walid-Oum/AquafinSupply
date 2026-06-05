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
}
