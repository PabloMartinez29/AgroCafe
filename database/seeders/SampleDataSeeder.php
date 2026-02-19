<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CoffeeType;
use App\Models\Cooperative;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    /**
     * Ejecutar el seeder de datos de ejemplo.
     * Crea datos de prueba para desarrollo
     */
    public function run(): void
    {
        // Crear campesinos de ejemplo
        $peasants = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@example.com',
                'password' => Hash::make('password123'),
                'phone' => '3001111111',
                'address' => 'Finca La Esperanza, Vereda El Cafetal',
                'role' => 'peasant',
                'active' => true,
            ],
            [
                'name' => 'María González',
                'email' => 'maria.gonzalez@example.com',
                'password' => Hash::make('password123'),
                'phone' => '3002222222',
                'address' => 'Finca San José, Vereda La Montaña',
                'role' => 'peasant',
                'active' => true,
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@example.com',
                'password' => Hash::make('password123'),
                'phone' => '3003333333',
                'address' => 'Finca El Paraíso, Vereda Los Alpes',
                'role' => 'peasant',
                'active' => true,
            ],
        ];

        foreach ($peasants as $peasantData) {
            User::firstOrCreate(
                ['email' => $peasantData['email']],
                $peasantData
            );
        }

        $this->command->info('✅ Campesinos de ejemplo creados');

        // Crear tipos de café
        $coffeeTypes = [
            [
                'name' => 'Café Arábica Premium',
                'variety' => 'arabica',
                'quality' => 'premium',
                'processing_type' => 'wet',
                'base_price' => 8000,
                'description' => 'Café de alta calidad, procesado en húmedo',
                'active' => true,
            ],
            [
                'name' => 'Café Arábica Especial',
                'variety' => 'arabica',
                'quality' => 'special',
                'processing_type' => 'normal',
                'base_price' => 6500,
                'description' => 'Café especial de variedad arábica',
                'active' => true,
            ],
            [
                'name' => 'Café Robusta Comercial',
                'variety' => 'robusta',
                'quality' => 'commercial',
                'processing_type' => 'dry',
                'base_price' => 5000,
                'description' => 'Café robusta para uso comercial',
                'active' => true,
            ],
            [
                'name' => 'Café Pasilla',
                'variety' => 'arabica',
                'quality' => 'commercial',
                'processing_type' => 'pasilla',
                'base_price' => 4500,
                'description' => 'Café pasilla de buena calidad',
                'active' => true,
            ],
        ];

        foreach ($coffeeTypes as $typeData) {
            CoffeeType::firstOrCreate(
                ['name' => $typeData['name']],
                $typeData
            );
        }

        $this->command->info('✅ Tipos de café creados');

        // Crear cooperativas
        $cooperatives = [
            [
                'name' => 'Cooperativa de Caficultores del Sur',
                'nit' => '900123456-1',
                'phone' => '6012345678',
                'email' => 'contacto@coopsur.com',
                'address' => 'Calle Principal #123, Municipio del Sur',
                'legal_representative' => 'Pedro Martínez',
                'active' => true,
            ],
            [
                'name' => 'Asociación de Productores de Café',
                'nit' => '900234567-1',
                'phone' => '6012345679',
                'email' => 'info@asocafe.com',
                'address' => 'Avenida Central #456, Ciudad',
                'legal_representative' => 'Ana López',
                'active' => true,
            ],
        ];

        foreach ($cooperatives as $coopData) {
            Cooperative::firstOrCreate(
                ['nit' => $coopData['nit']],
                $coopData
            );
        }

        $this->command->info('✅ Cooperativas creadas');
        $this->command->info('✅ Datos de ejemplo creados exitosamente!');
    }
}

