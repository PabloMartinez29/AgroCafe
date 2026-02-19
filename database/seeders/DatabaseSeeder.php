<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeders
        $this->call([
            AdminSeeder::class,
            // Descomentar para crear datos de ejemplo
            // SampleDataSeeder::class,
        ]);
    }
}
