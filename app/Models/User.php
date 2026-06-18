<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model
 * 
 * Vertegenwoordigt een gebruiker in de applicatie.
 * Ondersteunt drie rollen: technieker, magazijnmedewerker en administrator.
 * Gebruikers zijn gekoppeld aan een locatie (depot) en kunnen tickets,
 * bestellingen en notificaties hebben.
 *
 * @author 
 * @version 1.0
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Relatie: een gebruiker heeft meerdere tickets (supportaanvragen).
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id', 'id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'location_id',
        'must_change_password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relatie: een gebruiker heeft meerdere bestellingen.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relatie: een gebruiker hoort bij een locatie (depot).
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Relatie: een gebruiker heeft meerdere notificaties.
     */
    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class);
    }
}