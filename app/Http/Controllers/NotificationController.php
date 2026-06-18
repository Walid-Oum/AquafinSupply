<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;

class NotificationController extends Controller
{
    /**
     * Toon het overzicht van notificaties voor de ingelogde gebruiker.
     * Haalt alle notificaties op en markeert ongelezen notificaties direct als gelezen.
     */
    public function index()
    {
        /**  Haal alle notificaties van de ingelogde gebruiker op, gesorteerd van nieuw naar oud */
        $notifications = UserNotification::where('user_id', auth()->id())
            ->latest()
            ->get();

        UserNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }
/**
     * API Eindpunt: Markeer alle ongelezen notificaties van de gebruiker als gelezen.
     * Wordt typisch aangeroepen via een AJAX-request (bijv. bij het openen van een notificatie-dropdown).
     */
    public function markAsRead()
    {
        UserNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
        ]);
    }
}
