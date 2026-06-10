<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [

        'user_id',
        'delivery_date',
        'comment',
        'status',
        'location_id'

    ];
    public function user()
{
    return $this->belongsTo(User::class);
}

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function location(){
        return $this->belongsTo(Location::class);
    }
}
