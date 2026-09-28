<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan penting: unit harus ada sebelum akun petugas unit.
        $this->call([
            UnitSeeder::class,
            DataAwalSeeder::class,
        ]);

        // Akun demo hanya untuk development.
        if (app()->environment('local')) {
            $this->call(AkunDemoSeeder::class);
        }
    }
}
