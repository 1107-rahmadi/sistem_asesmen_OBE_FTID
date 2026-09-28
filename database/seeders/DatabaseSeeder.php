<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed data master: Prodi -> CPL -> CPMK -> Matriks CPMK
        // Urutan penting karena ada foreign key!
        $this->call([
            ProdiSeeder::class,
            CplSeeder::class,
            CpmkSeeder::class,
            MatriksCpmkSeeder::class,
        ]);
    }
}
