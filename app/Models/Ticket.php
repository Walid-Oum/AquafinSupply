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
        'location_id'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function location(){
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    
    public function scopeSearch($query, $searchTerm)
    {
        if (!$searchTerm) {
            return $query;
        }

        // Maak een spatieloze zoekterm (bijv. van "storing pomp" naar "storingpomp")
        $cleanSearch = str_replace(' ', '', $searchTerm);

        return $query->where(function ($q) use ($searchTerm, $cleanSearch) {
            $q->whereRaw("REPLACE(subject, ' ', '') LIKE ?", ["%{$cleanSearch}%"])
              ->orWhereRaw("REPLACE(description, ' ', '') LIKE ?", ["%{$cleanSearch}%"])
              ->orWhereRaw("REPLACE(status, ' ', '') LIKE ?", ["%{$cleanSearch}%"]);
        })
        // PRIORITEIT: Exacte matches of items waarvan het ONDERWERP begint met de zoekterm komen bovenaan (score 2)
        // Gedeeltelijke matches komen daaronder (score 1), de rest onderaan (score 0)
        ->orderByRaw("
            CASE 
                WHEN subject LIKE ? THEN 2
                WHEN subject LIKE ? THEN 1
                ELSE 0
            END DESC
        ", ["{$searchTerm}", "%{$searchTerm}%"]);
    }
}