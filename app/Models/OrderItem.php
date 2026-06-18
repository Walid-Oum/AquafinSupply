<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * OrderItem Model
 * 
 * Vertegenwoordigt een regel binnen een bestelling.
 * Elke orderitem koppelt een materiaal aan een bestelling met een specifieke hoeveelheid.
 *
 * @author 
 * @version 1.0
 */
class OrderItem extends Model
{
    /**
     * Velden die massaal mogen worden ingevuld.
     */
    protected $fillable = [
        'order_id',
        'material_id',
        'quantity'
    ];

    /**
     * Relatie: deze orderitem hoort bij een bestelling.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relatie: deze orderitem hoort bij een materiaal.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}