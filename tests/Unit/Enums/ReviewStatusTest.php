<?php

declare(strict_types=1);

use App\Enums\ReviewStatus;

it('enforces the expected review status transition matrix', function () {
    $allowedTransitions = [
        ReviewStatus::PENDING->value => [ReviewStatus::IN_PROGRESS],
        ReviewStatus::IN_PROGRESS->value => [ReviewStatus::COMPLETED],
        ReviewStatus::COMPLETED->value => [ReviewStatus::RE_REVIEW_REQUESTED],
        ReviewStatus::RE_REVIEW_REQUESTED->value => [ReviewStatus::IN_PROGRESS],
    ];

    foreach (ReviewStatus::cases() as $fromStatus) {
        $expectedTargets = $allowedTransitions[$fromStatus->value];

        foreach (ReviewStatus::cases() as $toStatus) {
            $shouldAllow = in_array($toStatus, $expectedTargets, true);

            expect($fromStatus->canTransitionTo($toStatus))->toBe($shouldAllow);
        }
    }
});

it('returns labels, colors, icons, and descriptions', function () {
    expect(ReviewStatus::PENDING->label())->toBe('Menunggu Review');
    expect(ReviewStatus::IN_PROGRESS->color())->toBe('info');
    expect(ReviewStatus::COMPLETED->icon())->toBe('check-circle');
    expect(ReviewStatus::RE_REVIEW_REQUESTED->description())->toContain('review ulang');
});

it('marks action-required and finished states correctly', function () {
    expect(ReviewStatus::PENDING->requiresAction())->toBeTrue();
    expect(ReviewStatus::RE_REVIEW_REQUESTED->requiresAction())->toBeTrue();
    expect(ReviewStatus::IN_PROGRESS->requiresAction())->toBeFalse();
    expect(ReviewStatus::COMPLETED->isFinished())->toBeTrue();
    expect(ReviewStatus::PENDING->isFinished())->toBeFalse();
});

it('returns action-required list, filter options, and values', function () {
    expect(ReviewStatus::actionRequired())->toBe([
        ReviewStatus::PENDING,
        ReviewStatus::RE_REVIEW_REQUESTED,
    ]);

    $filterOptions = ReviewStatus::filterOptions();
    expect($filterOptions['all'])->toBe('Semua Status');

    foreach (ReviewStatus::cases() as $status) {
        expect($filterOptions)->toHaveKey($status->value);
        expect($filterOptions[$status->value])->toBe($status->label());
    }

    expect(ReviewStatus::values())->toBe(array_map(
        static fn (ReviewStatus $status): string => $status->value,
        ReviewStatus::cases()
    ));
});
