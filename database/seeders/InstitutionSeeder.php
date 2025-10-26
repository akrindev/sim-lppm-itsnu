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
        $institutions = [
            'Institut Teknologi dan Sains Nahdlatul Ulama Pekalongan',
            'Universitas Islam Negeri Walisongo Semarang',
            'Universitas Diponegoro',
            'Universitas Gadjah Mada',
            'Institut Teknologi Bandung',
            'Universitas Indonesia',
            'Institut Teknologi Sepuluh Nopember',
            'Universitas Airlangga',
            'Universitas Brawijaya',
            'Universitas Sebelas Maret',
            'Universitas Negeri Semarang',
            'Universitas Jenderal Soedirman',
        ];

        foreach ($institutions as $institution) {
            \App\Models\Institution::create(['name' => $institution]);
        }
    }
}
