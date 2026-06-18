<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Order Model
 * 
 * Vertegenwoordigt een bestelling in de applicatie.
 * Een bestelling wordt aangemaakt door een technieker en bevat meerdere orderitems.
 * De status kan worden gewijzigd door het magazijn.
 *
 * @author 
 * @version 1.0
 */
class Order extends Model
{
    /**
     * Velden die massaal mogen worden ingevuld.
     */
    protected $fillable = [
        'user_id',
        'delivery_date',
        'comment',
        'status',
        'location_id'
    ];

    /**
     * Relatie: deze bestelling hoort bij een gebruiker (technieker).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relatie: deze bestelling bevat meerdere orderitems (materialen).
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relatie: deze bestelling hoort bij een locatie (depot).
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Scope: zoekfunctie voor bestellingen.
     * Zoekt op ID, status, comment of technieker naam.
     * Geeft prioriteit aan exacte status matches en specifieke ID's.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $searchTerm
     * @return \Illuminate\Database\Eloquent\Builder
     */
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