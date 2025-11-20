<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Crea un usuario administrador para pruebas (solo en entornos no productivos).
     */
    public function run(): void
    {
        // Solo ejecutar en entornos de desarrollo/testing (no en producción)
        if (app()->environment('production')) {
            $this->command->warn('⚠️  AdminUserSeeder omitido en producción por seguridad.');
            return;
        }

        $email = 'gmaster@example.com';

        // Verificar si el usuario ya existe
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            $this->command->info("ℹ️  Usuario administrador '{$email}' ya existe.");

            // Asegurar que tenga privilegios de admin
            if (!$existingUser->is_admin) {
                $existingUser->update(['is_admin' => true]);
                $this->command->info("✓ Privilegios de administrador otorgados a '{$email}'.");
            }
        } else {
            // Crear nuevo usuario administrador
            User::create([
                'name' => 'Gmaster',
                'email' => $email,
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]);

            $this->command->info("✓ Usuario administrador creado: {$email} / admin123");
        }
    }
}
