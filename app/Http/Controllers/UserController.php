<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Toon het overzicht van alle gebruikers voor de admin.
    public function index()
    {
        $users = User::with('location')->get();

        return view('admin.users.index', compact('users'));
    }

    // Toon het formulier om een nieuwe gebruiker aan te maken.
    public function create()
    {
        $roles = ['admin', 'magazijn', 'technieker'];

        $locations = Location::orderBy('province')
            ->orderBy('name')
            ->get();

        return view('admin.users.create', compact('roles', 'locations'));
    }

    // Sla de nieuwe gebruiker op in de database.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'email' => 'required|string|email|max:255|unique:users',

            'password' => 'required|string|min:8|confirmed',

            'role' => [
                'required',
                Rule::in(['admin', 'magazijn', 'technieker']),
            ],

            'location_id' => [
                'required',
                Rule::exists('locations', 'id'),
            ],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
            'location_id' => $validated['location_id'],
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Gebruiker succesvol aangemaakt!');
    }

    // Toon het formulier om een bestaande gebruiker aan te passen.
    public function edit(User $user)
    {
        $roles = ['admin', 'magazijn', 'technieker'];

        $locations = Location::orderBy('province')
            ->orderBy('name')
            ->get();

        return view('admin.users.edit', compact('user', 'roles', 'locations'));
    }

    // Update de gegevens van de gebruiker in de database.
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],

            'role' => [
                'required',
                Rule::in(['admin', 'magazijn', 'technieker']),
            ],

            'location_id' => [
                'required',
                Rule::exists('locations', 'id'),
            ],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'location_id' => $validated['location_id'],
        ]);

        // Alleen het wachtwoord updaten als er iets is ingevuld.
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);

            $user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Gebruiker succesvol aangepast!');
    }
}
