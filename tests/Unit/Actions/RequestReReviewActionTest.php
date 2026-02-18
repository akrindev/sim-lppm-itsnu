<?php

declare(strict_types=1);

use App\Enums\ProposalStatus;
use App\Enums\ReviewStatus;
use App\Livewire\Actions\RequestReReviewAction;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

function createProposalForReReview(array $attributes = []): Proposal
{
    $submitter = User::factory()->create();

    return Proposal::query()->create(array_merge([
        'title' => 'Request Re-Review Action Unit Test',
        'submitter_id' => $submitter->id,
        'status' => ProposalStatus::SUBMITTED,
    ], $attributes));
}

function createRequestReReviewActionSpy(): array
{
    $spy = new class extends NotificationService
    {
        public int $notifyProposalRevisedCalls = 0;

        public int $adminNotificationCalls = 0;

        public function notifyProposalRevised($proposal, User $recipient, int $round, bool $isAdmin = false): void
        {
            $this->notifyProposalRevisedCalls++;

            if ($isAdmin) {
                $this->adminNotificationCalls++;
            }
        }
    };

    return [$spy, new RequestReReviewAction($spy)];
}

it('rejects re-review request when no reviewers are assigned', function () {
    [$spy, $action] = createRequestReReviewActionSpy();

    $proposal = createProposalForReReview();

    $result = $action->execute($proposal);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('Tidak ada reviewer');
    expect($spy->notifyProposalRevisedCalls)->toBe(0);
});

it('updates all reviewers for new round and sends reviewer/admin notifications', function () {
    Carbon::setTestNow(Carbon::parse('2026-02-19 09:00:00'));

    Artisan::call('db:seed', ['--class' => \Database\Seeders\RoleSeeder::class]);

    $adminLppm = User::factory()->create();
    $adminLppm->assignRole('admin lppm');

    $kepalaLppm = User::factory()->create();
    $kepalaLppm->assignRole('kepala lppm');

    [$spy, $action] = createRequestReReviewActionSpy();

    $proposal = createProposalForReReview();
    $reviewerA = User::factory()->create();
    $reviewerB = User::factory()->create();

    ProposalReviewer::query()->create([
        'proposal_id' => $proposal->id,
        'user_id' => $reviewerA->id,
        'status' => ReviewStatus::COMPLETED,
        'round' => 1,
        'review_notes' => 'Catatan lama A',
        'recommendation' => 'approved',
        'started_at' => now()->subDays(10),
        'completed_at' => now()->subDays(8),
    ]);

    ProposalReviewer::query()->create([
        'proposal_id' => $proposal->id,
        'user_id' => $reviewerB->id,
        'status' => ReviewStatus::COMPLETED,
        'round' => 1,
        'review_notes' => 'Catatan lama B',
        'recommendation' => 'revision_needed',
        'started_at' => now()->subDays(9),
        'completed_at' => now()->subDays(7),
    ]);

    $result = $action->execute($proposal, 10);

    $reviewers = $proposal->fresh()->reviewers;

    expect($result['success'])->toBeTrue();
    expect($result['message'])->toContain('berhasil dikirim');
    expect($reviewers)->toHaveCount(2);

    foreach ($reviewers as $reviewer) {
        expect($reviewer->status)->toBe(ReviewStatus::RE_REVIEW_REQUESTED);
        expect($reviewer->round)->toBe(2);
        expect($reviewer->review_notes)->toBeNull();
        expect($reviewer->recommendation)->toBeNull();
        expect($reviewer->started_at)->toBeNull();
        expect($reviewer->completed_at)->toBeNull();
        expect($reviewer->assigned_at?->toDateTimeString())->toBe('2026-02-19 09:00:00');
        expect($reviewer->deadline_at?->toDateTimeString())->toBe('2026-03-01 09:00:00');
    }

    expect($spy->notifyProposalRevisedCalls)->toBe(4);
    expect($spy->adminNotificationCalls)->toBe(2);

    Carbon::setTestNow();
});
