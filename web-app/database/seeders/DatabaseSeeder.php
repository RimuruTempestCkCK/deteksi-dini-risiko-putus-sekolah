<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            KelasSeeder::class,
            SiswaSeeder::class,
            ModelEvaluasiSeeder::class,
            PrediksiSeeder::class,
        ]);
    }
}
