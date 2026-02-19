<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Ejecutar el seeder de base de datos.
     * Crea un usuario administrador por defecto
     */
    public function run(): void
    {
        // Verificar si ya existe un administrador
        $adminExists = User::where('email', 'admin@agrocafe.com')->exists();

        if (!$adminExists) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@agrocafe.com',
                'password' => Hash::make('admin123'), // Cambiar esta contraseña en producción
                'phone' => '3001234567',
                'address' => 'Dirección del administrador',
                'role' => 'admin',
                'active' => true,
                'email_verified_at' => now(),
            ]);

            $this->command->info('Usuario administrador creado exitosamente!');
            $this->command->info('Email: admin@agrocafe.com');
            $this->command->info('Contraseña: admin123');
            $this->command->warn('IMPORTANTE: Cambia la contraseña después del primer inicio de sesión!');
        } else {
            $this->command->warn('El usuario administrador ya existe.');
        }
    }
}
