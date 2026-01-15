<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DailyNote extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\DailyNoteFactory> */
    use HasFactory, HasUuids, InteractsWithMedia;

    protected $fillable = [
        'proposal_id',
        'activity_date',
        'activity_description',
        'progress_percentage',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'progress_percentage' => 'integer',
        ];
    }

    /**
     * Get the proposal that owns the daily note.
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Register media collections for this model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('evidence');
    }
}
