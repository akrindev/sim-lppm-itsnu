<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;

class CommunityService extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\CommunityServiceFactory> */
    use HasFactory, HasUuids, InteractsWithMedia, SoftDeletes;

    /**
     * The type of the auto-incrementing ID's primary key.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the ID is auto-incrementing.
     */
    public $incrementing = false;

    protected $fillable = [
        'macro_research_group_id',
        'partner_id',
        'partner_issue_summary',
        'solution_offered',
    ];

    /**
     * Get the macro research group for the community service.
     */
    public function macroResearchGroup(): BelongsTo
    {
        return $this->belongsTo(MacroResearchGroup::class);
    }

    /**
     * Get the partner for the community service.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the proposal that owns the community service details.
     */
    public function proposal(): MorphOne
    {
        return $this->morphOne(Proposal::class, 'detailable');
    }

    /**
     * Register media collections for this model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('substance_file')
            ->singleFile()
            ->acceptsFile(function (File $file): bool {
                $extension = strtolower(pathinfo($file->name, PATHINFO_EXTENSION));

                return in_array($extension, ['pdf', 'doc', 'docx'], true);
            });

        $this->addMediaCollection('activity_photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg']);
    }
}
