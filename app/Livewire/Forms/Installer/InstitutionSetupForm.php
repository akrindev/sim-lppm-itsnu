<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Installer;

use Livewire\Form;

class InstitutionSetupForm extends Form
{
    public string $institutionName = 'Institut Teknologi dan Sains Nahdlatul Ulama Pekalongan';

    public string $institutionShortName = 'ITSNU Pekalongan';

    public string $address = '';

    public string $phone = '';

    public string $email = 'info@itsnu.ac.id';

    public string $website = 'https://itsnu.ac.id';

    public array $faculties = [
        ['name' => 'SAINTEK', 'code' => 'SAINTEK'],
        ['name' => 'DEKABITA', 'code' => 'DEKABITA'],
    ];

    protected function rules(): array
    {
        return [
            'institutionName' => 'required|string|max:255',
            'institutionShortName' => 'required|string|max:100',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'faculties' => 'required|array|min:1',
            'faculties.*.name' => 'required|string|max:100',
            'faculties.*.code' => 'required|string|max:20',
        ];
    }

    protected function messages(): array
    {
        return [
            'institutionName.required' => 'Institution name is required',
            'faculties.required' => 'At least one faculty is required',
            'faculties.min' => 'At least one faculty is required',
            'faculties.*.name.required' => 'Faculty name is required',
            'faculties.*.code.required' => 'Faculty code is required',
        ];
    }

    public function addFaculty(): void
    {
        $this->faculties[] = ['name' => '', 'code' => ''];
    }

    public function removeFaculty(int $index): void
    {
        if (count($this->faculties) > 1) {
            unset($this->faculties[$index]);
            $this->faculties = array_values($this->faculties);
        }
    }

    public function getInstitutionData(): array
    {
        return [
            'name' => $this->institutionName,
            'short_name' => $this->institutionShortName,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
        ];
    }

    public function getFacultiesData(): array
    {
        return $this->faculties;
    }
}
