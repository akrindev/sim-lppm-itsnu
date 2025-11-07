<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MandatoryOutput extends Model
{
    /** @use HasFactory<\Database\Factories\MandatoryOutputFactory> */
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
        'status_type',
        'author_status',
        'journal_title',
        'issn',
        'eissn',
        'indexing_body',
        'journal_url',
        'article_title',
        'publication_year',
        'volume',
        'issue_number',
        'page_start',
        'page_end',
        'article_url',
        'doi',
        'document_file',
    ];

    /**
     * Get the progress report that owns the mandatory output.
     */
    public function progressReport(): BelongsTo
    {
        return $this->belongsTo(ProgressReport::class);
    }

    /**
     * Get the proposal output that this mandatory output is based on.
     */
    public function proposalOutput(): BelongsTo
    {
        return $this->belongsTo(ProposalOutput::class);
    }
}
