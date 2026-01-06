<?php

namespace App\Services;

use App\Enums\ProposalStatus;
use App\Livewire\Forms\ProposalForm;
use App\Models\CommunityService;
use App\Models\Proposal;
use App\Models\Research;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProposalService
{
    public function createProposal(ProposalForm $form, string $type, ?string $submitterId = null): Proposal
    {
        $submitterId = $submitterId ?? (string) Auth::id();

        return DB::transaction(function () use ($form, $type, $submitterId) {
            if ($type === 'research') {
                return $form->storeResearch($submitterId);
            }

            return $form->storeCommunityService($submitterId);
        });
    }

    public function updateProposal(Proposal $proposal, ProposalForm $form): void
    {
        $form->proposal = $proposal;
        $form->update();
    }

    public function deleteProposal(Proposal $proposal): void
    {
        DB::transaction(function () use ($proposal) {
            $proposal->teamMembers()->detach();
            $proposal->detailable?->delete();
            $proposal->delete();
        });
    }

    public function getProposalsWithFilters(array $filters): LengthAwarePaginator
    {
        $type = $filters['type'] ?? 'research';

        $query = $this->getBaseProposalQuery($type);

        if (isset($filters['search']) && $filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('submitter', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('detailable', function ($dq) use ($search) {
                        $dq->where('id', 'like', "%{$search}%");
                    });
            });
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['year']) && $filters['year'] !== '') {
            $query->whereYear('created_at', $filters['year']);
        }

        if (isset($filters['role']) && $filters['role'] !== '') {
            $this->applyRoleFilter($query, $filters['role']);
        }

        return $query->latest()->paginate(15);
    }

    public function getProposalsForReviewer(string $reviewerId): LengthAwarePaginator
    {
        return Proposal::query()
            ->whereHas('reviewers', function ($query) use ($reviewerId) {
                $query->where('user_id', $reviewerId);
            })
            ->with(['submitter.identity', 'detailable', 'reviewers'])
            ->latest()
            ->paginate(15);
    }

    public function getProposalStatistics(array $filters): array
    {
        $type = $filters['type'] ?? 'research';
        $query = $this->getBaseProposalQuery($type);

        $totalCount = $query->count();

        $statusStats = $query
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $allStatuses = array_map(fn ($status) => $status->value, ProposalStatus::cases());
        $emptyStats = array_fill_keys($allStatuses, 0);

        return [
            'total' => $totalCount,
            'by_status' => array_merge($emptyStats, $statusStats),
        ];
    }

    public function getAvailableYears(string $type): array
    {
        return $this->getBaseProposalQuery($type)
            ->selectRaw('YEAR(created_at) as year')
            ->groupBy('year')
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();
    }

    public function validateProposalBeforeSubmit(Proposal $proposal): void
    {
        if ($proposal->status !== ProposalStatus::DRAFT->value) {
            throw new \Exception('Hanya proposal dengan status draft yang dapat disubmit.');
        }

        if ($proposal->teamMembers()->where('status', '!=', 'accepted')->exists()) {
            throw new \Exception('Semua anggota tim harus menerima undangan sebelum proposal dapat disubmit.');
        }

        if ($proposal->teamMembers()->count() < 2) {
            throw new \Exception('Proposal harus memiliki minimal 2 anggota tim.');
        }
    }

    public function submitProposal(Proposal $proposal): void
    {
        $this->validateProposalBeforeSubmit($proposal);

        $notificationService = app(NotificationService::class);
        $submitter = $proposal->submitter;
        $recipients = $notificationService->getUsersByRole('dekan');

        DB::transaction(function () use ($proposal, $notificationService, $submitter, $recipients) {
            $proposal->update(['status' => ProposalStatus::SUBMITTED->value]);

            $notificationService->notifyProposalSubmitted($proposal, $submitter, $recipients);
        });
    }

    public function getProposalType(Proposal $proposal): string
    {
        return match ($proposal->detailable_type) {
            Research::class => 'research',
            CommunityService::class => 'community-service',
            default => 'research',
        };
    }

    protected function getBaseProposalQuery(string $type): Builder
    {
        $detailableType = match ($type) {
            'research' => Research::class,
            'community-service' => CommunityService::class,
            default => Research::class,
        };

        return Proposal::query()
            ->with(['submitter.identity.faculty', 'detailable'])
            ->whereHas('detailable', function ($query) use ($detailableType) {
                $query->where('detailable_type', $detailableType);
            });
    }

    protected function applyRoleFilter(Builder $query, string $role): void
    {
        $userId = (string) Auth::id();

        match ($role) {
            'submitter', 'ketua' => $query->where('submitter_id', $userId),
            'team_member', 'anggota' => $query->whereHas('teamMembers', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }),
            'reviewer' => $query->whereHas('reviewers', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }),
            default => null,
        };
    }
}
