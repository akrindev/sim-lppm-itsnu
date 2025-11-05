<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    /** @use HasFactory<\Database\Factories\InstitutionFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get all faculties for the institution.
     */
    public function faculties(): HasMany
    {
        return $this->hasMany(Faculty::class);
    }

    /**
     * Get all study programs for the institution (through faculties).
     */
    public function studyPrograms(): HasMany
    {
        return $this->hasManyThrough(StudyProgram::class, Faculty::class);
    }

    /**
     * Get all identities associated with the institution.
     */
    public function identities(): HasMany
    {
        return $this->hasMany(Identity::class);
    }
}
