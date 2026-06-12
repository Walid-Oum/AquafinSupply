<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    public function edit()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        if ($request->user()->role === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('success', 'Wachtwoord succesvol ingesteld.');
        }

        return redirect()->route('technician.materials.index')
            ->with('success', 'Wachtwoord succesvol ingesteld.');
    }
}
