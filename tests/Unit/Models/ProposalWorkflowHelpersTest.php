<?php

declare(strict_types=1);

use App\Enums\ProposalStatus;
use App\Enums\ReviewStatus;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

function createWorkflowProposal(array $attributes = []): Proposal
{
    $submitter = User::factory()->create();

    return Proposal::query()->create(array_merge([
        'title' => 'Workflow Unit Test Proposal',
        'submitter_id' => $submitter->id,
        'status' => ProposalStatus::DRAFT,
    ], $attributes));
}

it('treats proposal with no team members as accepted by default', function () {
    $proposal = createWorkflowProposal();

    expect($proposal->allTeamMembersAccepted())->toBeTrue();
    expect($proposal->pendingTeamMembers()->count())->toBe(0);
    expect($proposal->getPendingTeamMembers())->toHaveCount(0);
});

it('requires all team members to accept before submission helper passes', function () {
    $proposal = createWorkflowProposal();

    $leader = User::factory()->create();
    $memberAccepted = User::factory()->create();
    $memberPending = User::factory()->create();

    $proposal->teamMembers()->attach($leader->id, ['role' => 'ketua', 'status' => 'accepted']);
    $proposal->teamMembers()->attach($memberAccepted->id, ['role' => 'anggota', 'status' => 'accepted']);
    $proposal->teamMembers()->attach($memberPending->id, ['role' => 'anggota', 'status' => 'pending']);

    expect($proposal->allTeamMembersAccepted())->toBeFalse();
    expect($proposal->pendingTeamMembers()->count())->toBe(1);
    expect($proposal->getPendingTeamMembers())->toHaveCount(1);

    $proposal->teamMembers()->updateExistingPivot($memberPending->id, ['status' => 'accepted']);

    expect($proposal->fresh()->allTeamMembersAccepted())->toBeTrue();
    expect($proposal->fresh()->getPendingTeamMembers())->toHaveCount(0);
});

it('computes reviewer completion helpers correctly across mixed statuses', function () {
    $proposal = createWorkflowProposal(['status' => ProposalStatus::UNDER_REVIEW]);

    $reviewerA = User::factory()->create();
    $reviewerB = User::factory()->create();
    $reviewerC = User::factory()->create();

    ProposalReviewer::query()->create([
        'proposal_id' => $proposal->id,
        'user_id' => $reviewerA->id,
        'status' => ReviewStatus::COMPLETED,
        'round' => 1,
    ]);

    ProposalReviewer::query()->create([
        'proposal_id' => $proposal->id,
        'user_id' => $reviewerB->id,
        'status' => ReviewStatus::PENDING,
        'round' => 1,
    ]);

    ProposalReviewer::query()->create([
        'proposal_id' => $proposal->id,
        'user_id' => $reviewerC->id,
        'status' => ReviewStatus::RE_REVIEW_REQUESTED,
        'round' => 1,
    ]);

    expect($proposal->allReviewsCompleted())->toBeFalse();
    expect($proposal->allReviewersCompleted())->toBeFalse();
    expect($proposal->canBeApproved())->toBeFalse();
    expect($proposal->pendingReviewers()->count())->toBe(2);
    expect($proposal->getPendingReviewers())->toHaveCount(2);

    $proposal->reviewers()->update(['status' => ReviewStatus::COMPLETED]);

    expect($proposal->fresh()->allReviewsCompleted())->toBeTrue();
    expect($proposal->fresh()->allReviewersCompleted())->toBeTrue();
    expect($proposal->fresh()->canBeApproved())->toBeTrue();
    expect($proposal->fresh()->pendingReviewers()->count())->toBe(0);
    expect($proposal->fresh()->getPendingReviewers())->toHaveCount(0);
});

it('requires at least one reviewer for allReviewersCompleted helper', function () {
    $proposal = createWorkflowProposal(['status' => ProposalStatus::UNDER_REVIEW]);

    expect($proposal->allReviewersCompleted())->toBeFalse();
    expect($proposal->allReviewsCompleted())->toBeFalse();
});
