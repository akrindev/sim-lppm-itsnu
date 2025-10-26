<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchStage extends Model
{
    /** @use HasFactory<\Database\Factories\ResearchStageFactory> */
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'stage_number',
        'process_name',
        'outputs',
        'indicator',
        'person_in_charge_id',
    ];

    /**
     * Get the proposal that owns the research stage.
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Get the user who is in charge of this stage.
     */
    public function personInCharge(): BelongsTo
    {
        return $this->belongsTo(User::class, 'person_in_charge_id');
    }
}
