<?php

declare(strict_types=1);

use App\Enums\ProposalStatus;
use App\Enums\ReviewStatus;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

function createProposalReviewer(array $reviewerAttributes = []): ProposalReviewer
{
    $submitter = User::factory()->create();
    $reviewer = User::factory()->create();

    $proposal = Proposal::query()->create([
        'title' => 'Proposal Reviewer Model Unit Test',
        'submitter_id' => $submitter->id,
        'status' => ProposalStatus::UNDER_REVIEW,
    ]);

    return ProposalReviewer::query()->create(array_merge([
        'proposal_id' => $proposal->id,
        'user_id' => $reviewer->id,
        'status' => ReviewStatus::PENDING,
        'round' => 1,
        'assigned_at' => now(),
        'deadline_at' => now()->addDays(14),
    ], $reviewerAttributes));
}

it('marks review as started only once and updates status', function () {
    Carbon::setTestNow(Carbon::parse('2026-02-19 08:00:00'));

    $review = createProposalReviewer();

    $review->markAsStarted();

    expect($review->fresh()->status)->toBe(ReviewStatus::IN_PROGRESS);
    expect($review->fresh()->started_at?->toDateTimeString())->toBe('2026-02-19 08:00:00');
    expect($review->fresh()->isInProgress())->toBeTrue();

    Carbon::setTestNow(Carbon::parse('2026-02-19 10:00:00'));

    $review->fresh()->markAsStarted();

    expect($review->fresh()->started_at?->toDateTimeString())->toBe('2026-02-19 08:00:00');

    Carbon::setTestNow();
});

it('completes review with notes, recommendation, and completion timestamp', function () {
    Carbon::setTestNow(Carbon::parse('2026-02-19 09:15:00'));

    $review = createProposalReviewer();
    $review->markAsStarted();

    $review->fresh()->complete('Penilaian lengkap', 'approved');

    expect($review->fresh()->status)->toBe(ReviewStatus::COMPLETED);
    expect($review->fresh()->isCompleted())->toBeTrue();
    expect($review->fresh()->review_notes)->toBe('Penilaian lengkap');
    expect($review->fresh()->recommendation)->toBe('approved');
    expect($review->fresh()->completed_at?->toDateTimeString())->toBe('2026-02-19 09:15:00');

    Carbon::setTestNow();
});

it('resets fields and increments round when re-review is requested', function () {
    Carbon::setTestNow(Carbon::parse('2026-02-19 11:00:00'));

    $review = createProposalReviewer([
        'status' => ReviewStatus::COMPLETED,
        'round' => 2,
        'review_notes' => 'Catatan lama',
        'recommendation' => 'revision_needed',
        'started_at' => now()->subDay(),
        'completed_at' => now()->subHours(12),
    ]);

    $review->requestReReview();

    expect($review->fresh()->status)->toBe(ReviewStatus::RE_REVIEW_REQUESTED);
    expect($review->fresh()->isReReviewRequested())->toBeTrue();
    expect($review->fresh()->round)->toBe(3);
    expect($review->fresh()->review_notes)->toBeNull();
    expect($review->fresh()->recommendation)->toBeNull();
    expect($review->fresh()->started_at)->toBeNull();
    expect($review->fresh()->completed_at)->toBeNull();
    expect($review->fresh()->assigned_at?->toDateTimeString())->toBe('2026-02-19 11:00:00');

    Carbon::setTestNow();
});

it('resets for a new round with pending status and new deadline', function () {
    Carbon::setTestNow(Carbon::parse('2026-02-19 13:30:00'));

    $review = createProposalReviewer([
        'status' => ReviewStatus::COMPLETED,
        'round' => 1,
        'review_notes' => 'Selesai sebelumnya',
        'recommendation' => 'approved',
        'started_at' => now()->subDays(3),
        'completed_at' => now()->subDays(1),
    ]);

    $review->resetForNewRound(10);

    expect($review->fresh()->status)->toBe(ReviewStatus::PENDING);
    expect($review->fresh()->round)->toBe(2);
    expect($review->fresh()->review_notes)->toBeNull();
    expect($review->fresh()->recommendation)->toBeNull();
    expect($review->fresh()->started_at)->toBeNull();
    expect($review->fresh()->completed_at)->toBeNull();
    expect($review->fresh()->assigned_at?->toDateTimeString())->toBe('2026-02-19 13:30:00');
    expect($review->fresh()->deadline_at?->toDateTimeString())->toBe('2026-03-01 13:30:00');

    Carbon::setTestNow();
});

it('calculates overdue and deadline-approaching boundaries', function () {
    Carbon::setTestNow(Carbon::parse('2026-02-19 12:00:00'));

    $overdueReview = createProposalReviewer([
        'status' => ReviewStatus::PENDING,
        'deadline_at' => Carbon::parse('2026-02-18 12:00:00'),
    ]);

    $dueSoonReview = createProposalReviewer([
        'status' => ReviewStatus::PENDING,
        'deadline_at' => Carbon::parse('2026-02-22 12:00:00'),
    ]);

    $futureReview = createProposalReviewer([
        'status' => ReviewStatus::PENDING,
        'deadline_at' => Carbon::parse('2026-02-25 12:00:00'),
    ]);

    $completedReview = createProposalReviewer([
        'status' => ReviewStatus::COMPLETED,
        'deadline_at' => Carbon::parse('2026-02-18 12:00:00'),
        'completed_at' => Carbon::parse('2026-02-18 11:00:00'),
    ]);

    expect($overdueReview->fresh()->isOverdue())->toBeTrue();
    expect($overdueReview->fresh()->daysRemaining)->toBe(-1);
    expect($overdueReview->fresh()->daysOverdue)->toBe(1);

    expect($dueSoonReview->fresh()->isDeadlineApproaching())->toBeTrue();
    expect($dueSoonReview->fresh()->daysRemaining)->toBe(3);
    expect($dueSoonReview->fresh()->daysOverdue)->toBe(0);

    expect($futureReview->fresh()->isDeadlineApproaching())->toBeFalse();
    expect($futureReview->fresh()->isOverdue())->toBeFalse();

    expect($completedReview->fresh()->isOverdue())->toBeFalse();
    expect($completedReview->fresh()->isDeadlineApproaching())->toBeFalse();

    Carbon::setTestNow();
});

it('supports reviewer workflow scopes', function () {
    Carbon::setTestNow(Carbon::parse('2026-02-19 12:00:00'));

    $pendingReview = createProposalReviewer([
        'status' => ReviewStatus::PENDING,
        'deadline_at' => Carbon::parse('2026-02-21 12:00:00'),
    ]);

    $completedReview = createProposalReviewer([
        'status' => ReviewStatus::COMPLETED,
        'completed_at' => Carbon::parse('2026-02-19 10:00:00'),
    ]);

    $rereviewReview = createProposalReviewer([
        'status' => ReviewStatus::RE_REVIEW_REQUESTED,
        'round' => 2,
        'deadline_at' => Carbon::parse('2026-02-18 12:00:00'),
    ]);

    expect(ProposalReviewer::query()->pending()->pluck('id')->all())
        ->toContain($pendingReview->id)
        ->not->toContain($completedReview->id);

    expect(ProposalReviewer::query()->completed()->pluck('id')->all())
        ->toContain($completedReview->id)
        ->not->toContain($pendingReview->id);

    expect(ProposalReviewer::query()->requiresAction()->pluck('id')->all())
        ->toContain($pendingReview->id)
        ->toContain($rereviewReview->id)
        ->not->toContain($completedReview->id);

    expect(ProposalReviewer::query()->overdue()->pluck('id')->all())
        ->toContain($rereviewReview->id)
        ->not->toContain($completedReview->id);

    expect(ProposalReviewer::query()->deadlineApproaching(3)->pluck('id')->all())
        ->toContain($pendingReview->id);

    expect(ProposalReviewer::query()->forReviewer($pendingReview->user_id)->count())->toBe(1);
    expect(ProposalReviewer::query()->forRound(2)->pluck('id')->all())->toContain($rereviewReview->id);
    expect(ProposalReviewer::query()->currentRound()->first()?->round)->toBe(2);

    Carbon::setTestNow();
});
