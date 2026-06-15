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

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    
    public function scopeSearch($query, $searchTerm)
    {
        if (!$searchTerm) {
            return $query;
        }

        // Spatieloze zoekterm maken (bijv. van "in behandeling" naar "inbehandeling")
        $cleanSearch = str_replace(' ', '', $searchTerm);

        return $query->where(function ($q) use ($searchTerm, $cleanSearch) {
            $q->where('id', 'like', "%{$cleanSearch}%")
              ->orWhereRaw("REPLACE(status, ' ', '') LIKE ?", ["%{$cleanSearch}%"])
              ->orWhereRaw("REPLACE(comment, ' ', '') LIKE ?", ["%{$cleanSearch}%"])
              ->orWhereHas('user', function ($userQuery) use ($cleanSearch) {
                  $userQuery->whereRaw("REPLACE(name, ' ', '') LIKE ?", ["%{$cleanSearch}%"]);
              });
        })
        // PRIORITEIT: Exacte status matches of specifieke ID's vliegen direct naar boven!
        ->orderByRaw("
            CASE 
                WHEN id = ? THEN 3
                WHEN status LIKE ? THEN 2
                ELSE 0
            END DESC
        ", [$searchTerm, "{$searchTerm}"]);
    }
}