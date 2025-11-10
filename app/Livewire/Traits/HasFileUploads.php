<?php

declare(strict_types=1);

namespace App\Livewire\Traits;

use App\Models\ProgressReport;
use App\Models\MandatoryOutput;
use App\Models\AdditionalOutput;
use Illuminate\Support\Facades\Auth;

trait HasFileUploads
{
    // Progress Report document files
    public $substanceFile;

    // Temporary file uploads
    public array $tempMandatoryFiles = [];
    public array $tempAdditionalFiles = [];
    public array $tempAdditionalCerts = [];

    /**
     * Validate substance file upload
     */
    public function validateSubstanceFile(): void
    {
        $this->validate([
            'substanceFile' => 'nullable|file|mimes:pdf|max:10240',
        ]);
    }

    /**
     * Validate mandatory output file upload (journal article)
     */
    public function validateMandatoryFile(int $proposalOutputId): void
    {
        $this->validate([
            "tempMandatoryFiles.{$proposalOutputId}" => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);
    }

    /**
     * Validate additional output file upload (book document)
     */
    public function validateAdditionalFile(int $proposalOutputId): void
    {
        $this->validate([
            "tempAdditionalFiles.{$proposalOutputId}" => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);
    }

    /**
     * Validate additional output certificate upload
     */
    public function validateAdditionalCert(int $proposalOutputId): void
    {
        $this->validate([
            "tempAdditionalCerts.{$proposalOutputId}" => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
    }

    /**
     * Save substance file to media collection
     */
    protected function saveSubstanceFile(ProgressReport $report, string $reportType = 'progress'): void
    {
        if (! $this->substanceFile) {
            return;
        }

        $report->clearMediaCollection('substance_file');
        $report
            ->addMedia($this->substanceFile->getRealPath())
            ->usingName($this->substanceFile->getClientOriginalName())
            ->usingFileName($this->substanceFile->hashName())
            ->withCustomProperties([
                'uploaded_by' => Auth::id(),
                'proposal_id' => $report->proposal_id,
                'report_type' => $reportType,
            ])
            ->toMediaCollection('substance_file');
    }

    /**
     * Save mandatory output file (journal article)
     */
    protected function saveMandatoryOutputFile(MandatoryOutput $output, int $proposalOutputId, string $reportType = 'progress'): void
    {
        if (! isset($this->tempMandatoryFiles[$proposalOutputId])) {
            return;
        }

        $output->clearMediaCollection('journal_article');
        $output
            ->addMedia($this->tempMandatoryFiles[$proposalOutputId]->getRealPath())
            ->usingName($this->tempMandatoryFiles[$proposalOutputId]->getClientOriginalName())
            ->usingFileName($this->tempMandatoryFiles[$proposalOutputId]->hashName())
            ->withCustomProperties([
                'uploaded_by' => Auth::id(),
                'proposal_id' => $output->progressReport->proposal_id,
                'report_type' => $reportType,
            ])
            ->toMediaCollection('journal_article');
    }

    /**
     * Save additional output file (book document)
     */
    protected function saveAdditionalOutputFile(AdditionalOutput $output, int $proposalOutputId, string $reportType = 'progress'): void
    {
        if (! isset($this->tempAdditionalFiles[$proposalOutputId])) {
            return;
        }

        $output->clearMediaCollection('book_document');
        $output
            ->addMedia($this->tempAdditionalFiles[$proposalOutputId]->getRealPath())
            ->usingName($this->tempAdditionalFiles[$proposalOutputId]->getClientOriginalName())
            ->usingFileName($this->tempAdditionalFiles[$proposalOutputId]->hashName())
            ->withCustomProperties([
                'uploaded_by' => Auth::id(),
                'proposal_id' => $output->progressReport->proposal_id,
                'report_type' => $reportType,
            ])
            ->toMediaCollection('book_document');
    }

    /**
     * Save additional output certificate
     */
    protected function saveAdditionalOutputCert(AdditionalOutput $output, int $proposalOutputId, string $reportType = 'progress'): void
    {
        if (! isset($this->tempAdditionalCerts[$proposalOutputId])) {
            return;
        }

        $output->clearMediaCollection('publication_certificate');
        $output
            ->addMedia($this->tempAdditionalCerts[$proposalOutputId]->getRealPath())
            ->usingName($this->tempAdditionalCerts[$proposalOutputId]->getClientOriginalName())
            ->usingFileName($this->tempAdditionalCerts[$proposalOutputId]->hashName())
            ->withCustomProperties([
                'uploaded_by' => Auth::id(),
                'proposal_id' => $output->progressReport->proposal_id,
                'report_type' => $reportType,
            ])
            ->toMediaCollection('publication_certificate');
    }

    /**
     * Clear substance file
     */
    public function clearSubstanceFile(): void
    {
        $this->reset('substanceFile');
    }

    /**
     * Clear mandatory file
     */
    public function clearMandatoryFile(int $proposalOutputId): void
    {
        unset($this->tempMandatoryFiles[$proposalOutputId]);
    }

    /**
     * Clear additional file
     */
    public function clearAdditionalFile(int $proposalOutputId): void
    {
        unset($this->tempAdditionalFiles[$proposalOutputId]);
    }

    /**
     * Clear additional certificate
     */
    public function clearAdditionalCert(int $proposalOutputId): void
    {
        unset($this->tempAdditionalCerts[$proposalOutputId]);
    }

    /**
     * Reset all file uploads
     */
    public function resetFileUploads(): void
    {
        $this->reset([
            'substanceFile',
            'tempMandatoryFiles',
            'tempAdditionalFiles',
            'tempAdditionalCerts',
        ]);
    }
}
