<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'name',
        'code',
    ];

    /**
     * Get the institution that owns the faculty.
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Get all study programs for the faculty.
     */
    public function studyPrograms(): HasMany
    {
        return $this->hasMany(StudyProgram::class);
    }

    /**
     * Get all identities associated with the faculty.
     */
    public function identities(): HasMany
    {
        return $this->hasMany(Identity::class);
    }
}
