<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    /**
     * Toon het formulier waarmee de gebruiker zijn wachtwoord kan wijzigen.
     */
    public function edit()
    {
        return view('auth.change-password');
    }
    /**
     * Verwerk het verzoek om het wachtwoord te wijzigen.
     * Controleert of het nieuwe wachtwoord verschilt van het huidige (tijdelijke) wachtwoord.
     */
    public function update(Request $request)
    {
        // Valideer de invoer: verplicht, minimaal 8 tekens en moet overeenkomen met het herhalingsveld (confirmed)
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (Hash::check($validated['password'], $user->password)) {
            return back()
                ->withErrors([
                    'password' => 'Je nieuwe wachtwoord mag niet hetzelfde zijn als je tijdelijke wachtwoord.',
                ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);
// Leid de gebruiker na de wijziging om naar het juiste startdashboard op basis van de gebruikersrol
        if ($user->role === 'admin') {
            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Wachtwoord succesvol ingesteld.');
        }

        if ($user->role === 'magazijn') {
            return redirect()
                ->route('magazijn.orders.index')
                ->with('success', 'Wachtwoord succesvol ingesteld.');
        }
// Standaard fallback route voor de rol van technieker
        return redirect()
            ->route('technician.materials.index')
            ->with('success', 'Wachtwoord succesvol ingesteld.');
    }
}
