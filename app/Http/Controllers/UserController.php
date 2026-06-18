<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Toon het overzicht van alle gebruikers voor de admin.
     */
    public function index(Request $request)
    {
        // Haal de gebruikers op inclusief hun gekoppelde locatie (voorkomt N+1 queries)
        $query = User::with('location');

        // Filter optioneel op rol als deze is meegegeven in het request (bijv. ?role=magazijn)
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Toon het formulier om een nieuwe gebruiker aan te maken.
     */
    public function create()
    {
        // De beschikbare rollen binnen de applicatie
        $roles = ['admin', 'magazijn', 'technieker'];

        // Haal alle locaties op, netjes gesorteerd op provincie en daarna op naam
        $locations = Location::orderBy('province')
            ->orderBy('name')
            ->get();

        return view('admin.users.create', compact('roles', 'locations'));
    }

    /**
     * Sla de nieuwe gebruiker op in de database.
     */
    public function store(Request $request)
    {
        // 1. Valideer de binnenkomende gegevens voor de nieuwe gebruiker
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // E-mail moet uniek zijn in de users tabel
            'password' => 'required|string|min:8|confirmed',          // Moet minstens 8 tekens zijn en matchen met password_confirmation
            'role' => [
                'required',
                Rule::in(['admin', 'magazijn', 'technieker']),       // Mag alleen een van deze drie rollen zijn
            ],
            'location_id' => [
                'required',
                Rule::exists('locations', 'id'),                     // Moet een bestaand ID in de locations tabel zijn
            ],
        ]);

        // 2. Maak de gebruiker aan in de database
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),              // Wachtwoord altijd veilig hashen
            'role' => $validated['role'],
            'location_id' => $validated['location_id'],
            // Business Rule: Iedereen behalve admins moet bij de eerste inlog verplicht het wachtwoord wijzigen
            'must_change_password' => $validated['role'] !== 'admin',
            'is_active' => true,                                      // Nieuwe gebruikers zijn standaard actief
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Gebruiker succesvol aangemaakt!');
    }

    /**
     * Toon het formulier om een bestaande gebruiker aan te passen.
     */
    public function edit(User $user)
    {
        $roles = ['admin', 'magazijn', 'technieker'];

        // Haal de locaties op voor de dropdown, alfabetisch gesorteerd
        $locations = Location::orderBy('province')
            ->orderBy('name')
            ->get();

        return view('admin.users.edit', compact('user', 'roles', 'locations'));
    }

    /**
     * Update de gegevens van de gebruiker in de database.
     */
    public function update(Request $request, User $user)
    {
        // 1. Valideer de profielgegevens
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),             // Uniek, behalve voor de gebruiker die we nu aanpassen
            ],
            // Zelfbescherming: Als de admin zichzelf bewerkt, is de rol optioneel (wordt later genegeerd)
            'role' => auth()->id() == $user->id
                ? ['nullable', Rule::in(['admin', 'magazijn', 'technieker'])]
                : ['required', Rule::in(['admin', 'magazijn', 'technieker'])],
            
            'location_id' => [
                'required',
                Rule::exists('locations', 'id'),
            ],
        ]);

        // Beveiliging: Voorkom dat een admin per ongeluk zijn eigen rol aanpast (en zichzelf uitsluit)
        if (auth()->id() == $user->id) {
            unset($validated['role']);                                // Verwijder de rol uit de invullijst
        }

        // 2. Update de basisgegevens van de gebruiker
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'] ?? $user->role,               // Behoud oude rol als deze is weggestript
            'location_id' => $validated['location_id'],
            'is_active' => $request->has('is_active') ? $request->is_active : $user->is_active,
        ]);

        // 3. Optioneel: Alleen het wachtwoord updaten als er daadwerkelijk iets is ingevuld
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);

            $user->update([
                'password' => bcrypt($request->password),
                // Als het wachtwoord gereset wordt, moet een non-admin deze bij de volgende inlog weer wijzigen
                'must_change_password' => ($validated['role'] ?? $user->role) !== 'admin',
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Gebruiker succesvol aangepast!');
    }

    /**
     * Deactiveer of activeer een gebruiker (alleen admin).
     */
    public function toggleActive($id)
    {
        $user = User::findOrFail($id);

        // Beveiliging: Een admin mag NOOIT zijn eigen account deactiveren
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Je kunt je eigen account niet deactiveren!');
        }

        // Inverteer de status (true wordt false / false wordt true)
        $user->is_active = !$user->is_active;
        $user->save();

        // Bepaal de juiste status voor de notificatiebalk
        $status = $user->is_active ? 'geactiveerd' : 'gedeactiveerd';
        
        return redirect()
            ->route('admin.users.index')
            ->with('success', "Gebruiker {$status}!");
    }
}