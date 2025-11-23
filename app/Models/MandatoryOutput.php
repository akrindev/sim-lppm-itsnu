<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MandatoryOutput extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\MandatoryOutputFactory> */
    use HasFactory, HasUuids, InteractsWithMedia;

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
        // New fields
        'book_title',
        'isbn',
        'authors',
        'publisher',
        'total_pages',
        'hki_type',
        'registration_number',
        'inventors',
        'product_name',
        'description',
        'readiness_level',
        'implementation_location',
        'media_name',
        'media_url',
        'publication_date',
        'video_url',
        'platform',
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

    /**
     * Register media collections for this model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('journal_article')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }
}
