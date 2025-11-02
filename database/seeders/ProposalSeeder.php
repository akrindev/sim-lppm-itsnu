<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProposalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Call the specific proposal seeders
        $this->call([
            ResearchSeeder::class,
            CommunityServiceSeeder::class,
        ]);
    }
}
