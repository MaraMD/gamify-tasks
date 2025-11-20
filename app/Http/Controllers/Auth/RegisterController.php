<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Crear Character por defecto
        $user->characters()->create([
            'name' => 'Héroe',
            'xp' => 0,
            'skin_hex' => '#ffffff',
            'boxer_hex' => '#6c757d',
            'hair_style' => 'none',
            'hair_hex' => '#000000',
            'eyes_hex' => '#2b2b2b',
        ]);

        Auth::login($user);

        return redirect()->route('character.show')
            ->with('status', '¡Bienvenido! Personaliza tu avatar.');
    }
}
