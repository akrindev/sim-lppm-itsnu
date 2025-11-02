<?php

namespace App\Enums;

enum ProposalStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case NEED_ASSIGNMENT = 'need_assignment';
    case APPROVED = 'approved';
    case UNDER_REVIEW = 'under_review';
    case REVIEWED = 'reviewed';
    case REVISION_NEEDED = 'revision_needed';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    /**
     * Mendapatkan label dalam bahasa Indonesia
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Diajukan',
            self::NEED_ASSIGNMENT => 'Perlu Persetujuan Anggota',
            self::APPROVED => 'Disetujui Dekan',
            self::UNDER_REVIEW => 'Sedang Direview',
            self::REVIEWED => 'Dalam Review',
            self::REVISION_NEEDED => 'Perlu Revisi',
            self::COMPLETED => 'Selesai',
            self::REJECTED => 'Ditolak',
        };
    }

    /**
     * Mendapatkan warna badge untuk status
     */
    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::SUBMITTED => 'info',
            self::NEED_ASSIGNMENT => 'warning',
            self::APPROVED => 'primary',
            self::UNDER_REVIEW => 'cyan',
            self::REVIEWED => 'orange',
            self::REVISION_NEEDED => 'yellow',
            self::COMPLETED => 'success',
            self::REJECTED => 'danger',
        };
    }

    /**
     * Validasi apakah bisa transisi ke status baru
     */
    public function canTransitionTo(ProposalStatus $newStatus): bool
    {
        return match ($this) {
            self::DRAFT => in_array($newStatus, [self::SUBMITTED]),
            self::SUBMITTED => in_array($newStatus, [self::APPROVED, self::NEED_ASSIGNMENT, self::REJECTED]),
            self::NEED_ASSIGNMENT => in_array($newStatus, [self::SUBMITTED]),
            self::APPROVED => in_array($newStatus, [self::UNDER_REVIEW, self::REJECTED]),
            self::UNDER_REVIEW => in_array($newStatus, [self::REVIEWED]),
            self::REVIEWED => in_array($newStatus, [self::COMPLETED, self::REVISION_NEEDED, self::REJECTED]),
            self::REVISION_NEEDED => in_array($newStatus, [self::SUBMITTED]),
            self::COMPLETED => false,
            self::REJECTED => false,
        };
    }

    /**
     * Mendapatkan deskripsi status
     */
    public function description(): string
    {
        return match ($this) {
            self::DRAFT => 'Proposal sedang dalam tahap penyusunan',
            self::SUBMITTED => 'Proposal telah diajukan dan menunggu persetujuan Dekan',
            self::NEED_ASSIGNMENT => 'Proposal memerlukan persetujuan anggota tim',
            self::APPROVED => 'Proposal telah disetujui Dekan dan menunggu persetujuan Kepala LPPM',
            self::UNDER_REVIEW => 'Proposal sedang dalam proses review oleh reviewer',
            self::REVIEWED => 'Semua reviewer telah menyelesaikan review',
            self::REVISION_NEEDED => 'Proposal memerlukan perbaikan sebelum disetujui',
            self::COMPLETED => 'Proposal telah disetujui dan selesai',
            self::REJECTED => 'Proposal ditolak',
        };
    }

    /**
     * Mendapatkan semua status yang bisa ditampilkan di filter
     */
    public static function filterOptions(): array
    {
        return [
            'all' => 'Semua Status',
            self::DRAFT->value => self::DRAFT->label(),
            self::SUBMITTED->value => self::SUBMITTED->label(),
            self::NEED_ASSIGNMENT->value => self::NEED_ASSIGNMENT->label(),
            self::APPROVED->value => self::APPROVED->label(),
            self::UNDER_REVIEW->value => self::UNDER_REVIEW->label(),
            self::REVIEWED->value => self::REVIEWED->label(),
            self::REVISION_NEEDED->value => self::REVISION_NEEDED->label(),
            self::COMPLETED->value => self::COMPLETED->label(),
            self::REJECTED->value => self::REJECTED->label(),
        ];
    }
}
