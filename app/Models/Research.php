<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Research extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ResearchFactory> */
    use HasFactory, HasUuids, InteractsWithMedia;

    /**
     * The type of the auto-incrementing ID's primary key.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the ID is auto-incrementing.
     */
    public $incrementing = false;

    protected $table = 'research';

    protected $fillable = [
        'macro_research_group_id',
        'tkt_type',
        'background',
        'state_of_the_art',
        'methodology',
        'roadmap_data',
        'substance_file',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'roadmap_data' => 'array',
        ];
    }

    /**
     * Get the proposal that owns the research details.
     */
    public function proposal(): MorphOne
    {
        return $this->morphOne(Proposal::class, 'detailable');
    }

    /**
     * Get the macro research group that owns the research.
     */
    public function macroResearchGroup(): BelongsTo
    {
        return $this->belongsTo(MacroResearchGroup::class);
    }

    /**
     * Get the TKT levels for the research.
     */
    public function tktLevels(): BelongsToMany
    {
        return $this->belongsToMany(TktLevel::class, 'research_tkt_level')
            ->withPivot('percentage');
    }

    /**
     * Get the TKT indicators for the research.
     */
    public function tktIndicators(): BelongsToMany
    {
        return $this->belongsToMany(TktIndicator::class, 'research_tkt_indicator')
            ->withPivot('score');
    }

    /**
     * Register media collections for this model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('substance_file')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }
}
