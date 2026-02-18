<?php

declare(strict_types=1);

use App\Enums\ProposalStatus;
use App\Enums\ReviewStatus;
use App\Livewire\Actions\AssignReviewersAction;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

use function Pest\Laravel\actingAs;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

function createProposalForReviewerAssignment(array $attributes = []): Proposal
{
    $submitter = User::factory()->create();

    return Proposal::query()->create(array_merge([
        'title' => 'Assign Reviewer Action Unit Test',
        'submitter_id' => $submitter->id,
        'status' => ProposalStatus::WAITING_REVIEWER,
    ], $attributes));
}

function createAssignReviewersActionSpy(): array
{
    $spy = new class extends NotificationService
    {
        public int $notifyReviewerAssignedCalls = 0;

        public function notifyReviewerAssigned($proposal, $reviewer, string $deadline, Collection|array $recipients): void
        {
            $this->notifyReviewerAssignedCalls++;
        }
    };

    return [$spy, new AssignReviewersAction($spy)];
}

it('rejects reviewer assignment when proposal status is not assignable', function () {
    [$spy, $action] = createAssignReviewersActionSpy();

    $proposal = createProposalForReviewerAssignment(['status' => ProposalStatus::DRAFT]);
    $reviewer = User::factory()->create();

    $result = $action->execute($proposal, $reviewer->id);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('status menunggu penugasan reviewer');
    expect($proposal->fresh()->status)->toBe(ProposalStatus::DRAFT);
    expect($spy->notifyReviewerAssignedCalls)->toBe(0);
});

it('rejects assignment when reviewer does not exist', function () {
    [$spy, $action] = createAssignReviewersActionSpy();

    $proposal = createProposalForReviewerAssignment();

    $result = $action->execute($proposal, '00000000-0000-0000-0000-000000000000');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('Reviewer tidak ditemukan');
    expect($spy->notifyReviewerAssignedCalls)->toBe(0);
});

it('rejects duplicate reviewer assignment for the same proposal', function () {
    [$spy, $action] = createAssignReviewersActionSpy();

    $proposal = createProposalForReviewerAssignment(['status' => ProposalStatus::UNDER_REVIEW]);
    $reviewer = User::factory()->create();

    ProposalReviewer::query()->create([
        'proposal_id' => $proposal->id,
        'user_id' => $reviewer->id,
        'status' => ReviewStatus::PENDING,
        'round' => 1,
    ]);

    $result = $action->execute($proposal, $reviewer->id);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('Reviewer sudah ditugaskan');
    expect($proposal->reviewers()->count())->toBe(1);
    expect($spy->notifyReviewerAssignedCalls)->toBe(0);
});

it('assigns reviewer with deadline and transitions waiting_reviewer to under_review', function () {
    Carbon::setTestNow(Carbon::parse('2026-02-19 07:00:00'));

    Artisan::call('db:seed', ['--class' => \Database\Seeders\RoleSeeder::class]);

    [$spy, $action] = createAssignReviewersActionSpy();

    $proposal = createProposalForReviewerAssignment(['status' => ProposalStatus::WAITING_REVIEWER]);
    $reviewer = User::factory()->create();

    actingAs(User::factory()->create());

    $result = $action->execute($proposal, $reviewer->id);
    $assignment = $proposal->fresh()->reviewers()->first();

    expect($result['success'])->toBeTrue();
    expect($proposal->fresh()->status)->toBe(ProposalStatus::UNDER_REVIEW);
    expect($assignment)->not->toBeNull();
    expect($assignment?->status)->toBe(ReviewStatus::PENDING);
    expect($assignment?->round)->toBe(1);
    expect($assignment?->assigned_at?->toDateTimeString())->toBe('2026-02-19 07:00:00');
    expect($assignment?->deadline_at?->toDateTimeString())->toBe('2026-03-05 07:00:00');
    expect($spy->notifyReviewerAssignedCalls)->toBe(1);

    Carbon::setTestNow();
});

it('keeps current round when assigning additional reviewer in under_review', function () {
    Artisan::call('db:seed', ['--class' => \Database\Seeders\RoleSeeder::class]);

    [$spy, $action] = createAssignReviewersActionSpy();

    $proposal = createProposalForReviewerAssignment(['status' => ProposalStatus::UNDER_REVIEW]);
    $existingReviewer = User::factory()->create();
    $newReviewer = User::factory()->create();

    ProposalReviewer::query()->create([
        'proposal_id' => $proposal->id,
        'user_id' => $existingReviewer->id,
        'status' => ReviewStatus::COMPLETED,
        'round' => 2,
        'completed_at' => now(),
    ]);

    $result = $action->execute($proposal, $newReviewer->id);
    $newAssignment = $proposal->fresh()->reviewers()->where('user_id', $newReviewer->id)->first();

    expect($result['success'])->toBeTrue();
    expect($proposal->fresh()->status)->toBe(ProposalStatus::UNDER_REVIEW);
    expect($newAssignment?->round)->toBe(2);
    expect($spy->notifyReviewerAssignedCalls)->toBe(1);
});
