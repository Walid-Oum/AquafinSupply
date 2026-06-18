<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * UserNotification Model
 * 
 * Vertegenwoordigt een notificatie voor een gebruiker.
 * Notificaties worden gebruikt om gebruikers te informeren over
 * statuswijzigingen van bestellingen, tickets of andere gebeurtenissen.
 *
 * @author 
 * @version 1.0
 */
class UserNotification extends Model
{
    /**
     * Velden die massaal mogen worden ingevuld.
     */
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'link',
        'read_at',
    ];

    /**
     * Relatie: deze notificatie hoort bij een gebruiker.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor: controleert of de notificatie gelezen is.
     *
     * @return bool
     */
    public function getIsReadAttribute(): bool
    {
        return $this->read_at !== null;
    }
}