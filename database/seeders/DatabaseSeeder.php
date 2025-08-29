<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Categorie;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $batchSize = 5000; // Number of records to insert per batch
        $total = 1000000;   // Total records you want to insert

        for ($i = 0; $i < $total / $batchSize; $i++) {
            Categorie::factory()->count($batchSize)->create();
        }

        
    }
}
