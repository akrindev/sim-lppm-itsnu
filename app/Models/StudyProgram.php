<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyProgram extends Model
{
    /** @use HasFactory<\Database\Factories\StudyProgramFactory> */
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'faculty_id',
        'name',
    ];

    /**
     * Get the institution that owns the study program.
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Get the faculty that owns the study program.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get all identities in this study program.
     */
    public function identities(): HasMany
    {
        return $this->hasMany(Identity::class);
    }
}
