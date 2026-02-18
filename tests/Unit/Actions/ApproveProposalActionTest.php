<?php

declare(strict_types=1);

use App\Enums\ProposalStatus;
use App\Enums\ReviewStatus;
use App\Livewire\Actions\ApproveProposalAction;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

function createProposalForFinalDecision(array $attributes = []): Proposal
{
    $submitter = User::factory()->create();

    return Proposal::query()->create(array_merge([
        'title' => 'Approve Proposal Action Unit Test',
        'submitter_id' => $submitter->id,
        'status' => ProposalStatus::REVIEWED,
    ], $attributes));
}

it('rejects invalid final decision value', function () {
    $proposal = createProposalForFinalDecision();

    $result = app(ApproveProposalAction::class)->execute($proposal, 'approved');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('Keputusan harus');
});

it('rejects final decision when reviewers are not all completed', function () {
    $proposal = createProposalForFinalDecision();

    ProposalReviewer::query()->create([
        'proposal_id' => $proposal->id,
        'user_id' => User::factory()->create()->id,
        'status' => ReviewStatus::PENDING,
        'round' => 1,
    ]);

    $result = app(ApproveProposalAction::class)->execute($proposal, ProposalStatus::COMPLETED->value);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('reviewer masih belum menyelesaikan review');
    expect($proposal->fresh()->status)->toBe(ProposalStatus::REVIEWED);
});

it('updates proposal to completed when all reviewers have completed', function () {
    $proposal = createProposalForFinalDecision();
    $actor = User::factory()->create();
    actingAs($actor);

    ProposalReviewer::query()->create([
        'proposal_id' => $proposal->id,
        'user_id' => User::factory()->create()->id,
        'status' => ReviewStatus::COMPLETED,
        'round' => 1,
        'completed_at' => now(),
    ]);

    $result = app(ApproveProposalAction::class)->execute($proposal, ProposalStatus::COMPLETED->value);

    expect($result['success'])->toBeTrue();
    expect($result['message'])->toContain('berhasil disetujui');
    expect($proposal->fresh()->status)->toBe(ProposalStatus::COMPLETED);
});

it('updates proposal to rejected when all reviewers have completed', function () {
    $proposal = createProposalForFinalDecision();
    $actor = User::factory()->create();
    actingAs($actor);

    ProposalReviewer::query()->create([
        'proposal_id' => $proposal->id,
        'user_id' => User::factory()->create()->id,
        'status' => ReviewStatus::COMPLETED,
        'round' => 1,
        'completed_at' => now(),
    ]);

    $result = app(ApproveProposalAction::class)->execute($proposal, ProposalStatus::REJECTED->value);

    expect($result['success'])->toBeTrue();
    expect($result['message'])->toBe('Proposal ditolak.');
    expect($proposal->fresh()->status)->toBe(ProposalStatus::REJECTED);
});
