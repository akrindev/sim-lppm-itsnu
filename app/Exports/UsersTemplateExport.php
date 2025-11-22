<?php

namespace App\Exports;

use App\Models\Institution;
use App\Models\StudyProgram;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class UsersTemplateExport implements FromCollection, WithHeadings, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $institutions = Institution::first();
        $studyPrograms = StudyProgram::first();

        return collect([
            [
                'Contoh User',
                'user1@example.com',
                'password123',
                '122345678',
                'dosen',
                $institutions->name,
                $studyPrograms->name,
                '123456',
                'Jl. Contoh No. 1',
                '1990-01-01',
                'Surabaya',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'name',
            'email',
            'password',
            'nidn',
            'type',
            'inst',
            'prodi',
            'sinta',
            'address',
            'birthdate',
            'birthplace',
        ];
    }

    public function title(): string
    {
        return 'Template Import Users';
    }
}
