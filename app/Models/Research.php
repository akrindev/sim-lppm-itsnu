<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Research extends Model
{
    /** @use HasFactory<\Database\Factories\ResearchFactory> */
    use HasFactory, HasUuids;

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
        'final_tkt_target',
        'background',
        'state_of_the_art',
        'methodology',
        'roadmap_data',
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
}
