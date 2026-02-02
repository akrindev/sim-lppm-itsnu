<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if custom institution data was provided by the installer
        $customInstitution = cache('installer_institution_config');

        if ($customInstitution) {
            // Use custom institution data from installer
            \App\Models\Institution::firstOrCreate(
                ['name' => $customInstitution['name']],
                [
                    'short_name' => $customInstitution['short_name'] ?? null,
                    'address' => $customInstitution['address'] ?? null,
                    'phone' => $customInstitution['phone'] ?? null,
                    'email' => $customInstitution['email'] ?? null,
                    'website' => $customInstitution['website'] ?? null,
                ]
            );

            return;
        }

        // Default institution data
        $institutions = [
            'Institut Teknologi dan Sains Nahdlatul Ulama Pekalongan',
        ];

        foreach ($institutions as $institution) {
            \App\Models\Institution::firstOrCreate(['name' => $institution]);
        }
    }
}
