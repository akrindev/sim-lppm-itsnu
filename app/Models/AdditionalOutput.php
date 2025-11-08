<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalOutput extends Model
{
    /** @use HasFactory<\Database\Factories\AdditionalOutputFactory> */
    use HasFactory, HasUuids;

    /**
     * The type of the auto-incrementing ID's primary key.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the ID is auto-incrementing.
     */
    public $incrementing = false;

    protected $fillable = [
        'progress_report_id',
        'proposal_output_id',
        'status',
        'book_title',
        'publisher_name',
        'isbn',
        'publication_year',
        'total_pages',
        'publisher_url',
        'book_url',
        'document_file',
        'publication_certificate',
    ];

    /**
     * Get the progress report that owns the additional output.
     */
    public function progressReport(): BelongsTo
    {
        return $this->belongsTo(ProgressReport::class);
    }

    /**
     * Get the proposal output that this additional output is based on.
     */
    public function proposalOutput(): BelongsTo
    {
        return $this->belongsTo(ProposalOutput::class);
    }
}
