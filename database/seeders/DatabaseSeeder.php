<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in correct order to respect foreign key constraints
        $this->call([
            // 1. Roles & Permissions
            RoleSeeder::class,

            // 2. Master Data (No Dependencies)
            InstitutionSeeder::class,
            ResearchSchemeSeeder::class,
            FocusAreaSeeder::class,
            NationalPrioritySeeder::class,
            KeywordSeeder::class,
            PartnerSeeder::class,

            // 3. Hierarchical Data (Self-referencing)
            ScienceClusterSeeder::class,

            // 4. Dependent Master Data
            StudyProgramSeeder::class, // depends on institutions
            ThemeSeeder::class,        // depends on focus_areas
            TopicSeeder::class,        // depends on themes

            // 5. Users & Identities
            UserSeeder::class,

            // 6. Proposals & Related Data (depends on users and master data)
            ProposalSeeder::class, // Creates proposals with research/community service + all related data
        ]);
    }
}
