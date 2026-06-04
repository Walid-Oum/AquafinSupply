<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'subject',
        'description',
        'status',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    //wanneer order bestaan
//    public function order(){
//        return $this->belongsTo(Order::class, 'order_id', 'id');
//    }
}
