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
        // Get institution
        $institution = \App\Models\Institution::where('name', 'like', '%Institut Teknologi dan Sains Nahdlatul Ulama%')->first()
            ?? \App\Models\Institution::first();

        if (! $institution) {
            $this->command->warn('Tidak ada institusi yang ditemukan. Silakan jalankan InstitutionSeeder terlebih dahulu.');

            return;
        }

        // Create Superadmin
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
                'type' => 'dosen', // Defaulting to dosen type for system users if needed, or maybe just leave as is
                'institution_id' => $institution->id,
                // Admins might not belong to specific faculty/prodi, so nullable
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
