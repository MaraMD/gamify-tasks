<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    private function currentUser(): User
    {
        return auth()->user() ?? User::findOrFail(1);
    }

    public function show()
    {
        $user = $this->currentUser();
        // Crea el personaje si no existe
        $character = $user->getOrCreateMainCharacter();

        return view('characters.show', compact('character'));
    }

    public function update(Request $request)
    {
        $user = $this->currentUser();
        $character = $user->getOrCreateMainCharacter();

        $data = $request->validate([
            'name'   => ['required','string','max:100'],
            'avatar' => ['nullable','string'], // URL o texto simple
        ]);

        $character->update($data);

        return back()->with('status', 'Personaje actualizado.');
    }
}
