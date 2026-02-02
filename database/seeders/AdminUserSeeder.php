<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if custom admin config was provided by the installer
        $customAdmin = cache('installer_admin_config');

        // Get institution
        $institution = \App\Models\Institution::where('name', 'like', '%Institut Teknologi dan Sains Nahdlatul Ulama%')->first()
            ?? \App\Models\Institution::first();

        if (! $institution) {
            $this->command->warn('Tidak ada institusi yang ditemukan. Silakan jalankan InstitutionSeeder terlebih dahulu.');

            return;
        }

        if ($customAdmin) {
            // Use custom admin data from installer
            $adminUser = User::firstOrCreate(
                ['email' => $customAdmin['email']],
                [
                    'name' => $customAdmin['name'],
                    'password' => Hash::make($customAdmin['password']),
                    'email_verified_at' => now(),
                ]
            );

            if (! $adminUser->identity) {
                $adminUser->identity()->create([
                    'identity_id' => 'ADMIN',
                    'type' => 'dosen',
                    'institution_id' => $institution->id,
                    'address' => $institution->short_name ?? 'Institution Address',
                    'birthdate' => '2000-01-01',
                    'birthplace' => 'Institution City',
                ]);
            }

            $adminUser->assignRole('superadmin');

            $this->command->info("Admin user '{$customAdmin['name']}' created successfully!");

            return;
        }

        // Create Superadmin (default)
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@email.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'), // Change in production!
                'email_verified_at' => now(),
            ]
        );

        if (! $superadmin->identity) {
            $superadmin->identity()->create([
                'identity_id' => 'SUPERADMIN',
                'type' => 'dosen',
                'institution_id' => $institution->id,
                'address' => 'ITSNU Pekalongan',
                'birthdate' => '2000-01-01',
                'birthplace' => 'Pekalongan',
            ]);
        }

        $superadmin->assignRole('superadmin');

        // Create Admin LPPM
        $adminLppm = User::firstOrCreate(
            ['email' => 'admin-lppm@email.com'],
            [
                'name' => 'Admin LPPM',
                'password' => Hash::make('password'), // Change in production!
                'email_verified_at' => now(),
            ]
        );

        if (! $adminLppm->identity) {
            $adminLppm->identity()->create([
                'identity_id' => '1',
                'type' => 'dosen',
                'institution_id' => $institution->id,
                'address' => 'ITSNU Pekalongan',
                'birthdate' => '2000-01-01',
                'birthplace' => 'Pekalongan',
            ]);
        }

        $adminLppm->assignRole('admin lppm');

        $this->command->info('Admin users seeded successfully!');
    }
}
