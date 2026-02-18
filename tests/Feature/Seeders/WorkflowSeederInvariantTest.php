<?php

declare(strict_types=1);

use App\Enums\ProposalStatus;
use App\Enums\ReviewStatus;
use App\Models\Proposal;
use Illuminate\Support\Facades\Artisan;

it('keeps seeded workflow data consistent with proposal and reviewer invariants', function () {
    Artisan::call('db:seed', ['--class' => \Database\Seeders\DatabaseSeeder::class]);

    $proposals = Proposal::query()
        ->with(['teamMembers', 'reviewers', 'statusLogs'])
        ->whereIn('status', [
            ProposalStatus::SUBMITTED,
            ProposalStatus::NEED_ASSIGNMENT,
            ProposalStatus::WAITING_REVIEWER,
            ProposalStatus::UNDER_REVIEW,
            ProposalStatus::REVIEWED,
            ProposalStatus::REVISION_NEEDED,
            ProposalStatus::COMPLETED,
            ProposalStatus::REJECTED,
        ])
        ->get();

    expect($proposals->count())->toBeGreaterThan(0);

    foreach ($proposals as $proposal) {
        if ($proposal->status === ProposalStatus::SUBMITTED) {
            expect($proposal->allTeamMembersAccepted())->toBeTrue();
        }

        if ($proposal->status === ProposalStatus::NEED_ASSIGNMENT) {
            expect($proposal->pendingTeamMembers()->count())->toBeGreaterThan(0);
            expect(
                $proposal->statusLogs->contains(fn ($log): bool => $log->status_before === ProposalStatus::SUBMITTED
                    && $log->status_after === ProposalStatus::NEED_ASSIGNMENT
                )
            )->toBeTrue();
        }

        if ($proposal->status === ProposalStatus::WAITING_REVIEWER) {
            expect($proposal->reviewers->count())->toBe(0);
        }

        if ($proposal->status === ProposalStatus::UNDER_REVIEW) {
            expect($proposal->reviewers->count())->toBeGreaterThan(0);
            expect($proposal->reviewers->contains(fn ($reviewer): bool => $reviewer->status !== ReviewStatus::COMPLETED))->toBeTrue();
        }

        if ($proposal->status === ProposalStatus::REVIEWED || $proposal->status === ProposalStatus::REVISION_NEEDED) {
            expect($proposal->reviewers->count())->toBeGreaterThan(0);
            expect($proposal->reviewers->every(fn ($reviewer): bool => $reviewer->status === ReviewStatus::COMPLETED))->toBeTrue();
        }

        if ($proposal->status === ProposalStatus::COMPLETED) {
            expect($proposal->reviewers->count())->toBeGreaterThan(0);
            expect($proposal->reviewers->every(fn ($reviewer): bool => $reviewer->status === ReviewStatus::COMPLETED))->toBeTrue();
            expect($proposal->reviewers->every(fn ($reviewer): bool => $reviewer->completed_at !== null))->toBeTrue();
            expect($proposal->reviewers->every(fn ($reviewer): bool => $reviewer->round === 1))->toBeTrue();
        }

        if ($proposal->status === ProposalStatus::REJECTED && $proposal->reviewers->count() > 0) {
            expect($proposal->reviewers->every(fn ($reviewer): bool => $reviewer->status === ReviewStatus::COMPLETED))->toBeTrue();
        }
    }
});
