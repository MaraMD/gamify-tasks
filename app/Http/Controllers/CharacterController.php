<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AvatarInventoryService;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    private function currentUser(): User
    {
        return auth()->user();
    }

    public function show()
    {
        $user = $this->currentUser();
        // Crea el personaje si no existe
        $character = $user->getOrCreateMainCharacter();

        // Get available avatar assets using the new inventory service
        $inventory = app(AvatarInventoryService::class)->all();

        return view('character.edit', compact('character', 'inventory'));
    }

    public function update(Request $request)
    {
        $user = $this->currentUser();
        $character = $user->getOrCreateMainCharacter();

        $data = $request->validate([
            'name'        => ['nullable', 'string', 'max:100'],
            'avatar'      => ['nullable', 'string'],
            'skin_hex'    => ['nullable', 'regex:/^#([0-9a-fA-F]{6})$/'],
            'boxer_hex'   => ['nullable', 'regex:/^#([0-9a-fA-F]{6})$/'],
            'hair_style'  => ['nullable', 'in:none,buzz,short'],
            'hair_hex'    => ['nullable', 'regex:/^#([0-9a-fA-F]{6})$/'],
            'eyes_hex'    => ['nullable', 'regex:/^#([0-9a-fA-F]{6})$/'],
            // Kenney avatar layer files
            'body_file'   => ['nullable', 'string', 'max:255'],
            'eyes_file'   => ['nullable', 'string', 'max:255'],
            'hair_file'   => ['nullable', 'string', 'max:255'],
            'top_file'    => ['nullable', 'string', 'max:255'],
            'bottom_file' => ['nullable', 'string', 'max:255'],
            'acc_file'    => ['nullable', 'string', 'max:255'],
        ]);

        $character->update($data);

        return back()->with('status', 'Personaje actualizado.');
    }
}
