<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;

class NotificationController extends Controller
{
    public function index()
    {
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
}
