<?php

declare(strict_types=1);

use App\Enums\ProposalStatus;

it('enforces the expected proposal status transition matrix', function () {
    $allowedTransitions = [
        ProposalStatus::DRAFT->value => [ProposalStatus::SUBMITTED],
        ProposalStatus::SUBMITTED->value => [ProposalStatus::APPROVED, ProposalStatus::NEED_ASSIGNMENT, ProposalStatus::REJECTED],
        ProposalStatus::NEED_ASSIGNMENT->value => [ProposalStatus::SUBMITTED],
        ProposalStatus::APPROVED->value => [ProposalStatus::WAITING_REVIEWER, ProposalStatus::UNDER_REVIEW, ProposalStatus::REJECTED],
        ProposalStatus::WAITING_REVIEWER->value => [ProposalStatus::UNDER_REVIEW],
        ProposalStatus::UNDER_REVIEW->value => [ProposalStatus::REVIEWED],
        ProposalStatus::REVIEWED->value => [ProposalStatus::COMPLETED, ProposalStatus::REVISION_NEEDED, ProposalStatus::REJECTED],
        ProposalStatus::REVISION_NEEDED->value => [ProposalStatus::SUBMITTED],
        ProposalStatus::COMPLETED->value => [],
        ProposalStatus::REJECTED->value => [],
    ];

    foreach (ProposalStatus::cases() as $fromStatus) {
        $expectedTargets = $allowedTransitions[$fromStatus->value];

        foreach (ProposalStatus::cases() as $toStatus) {
            $shouldAllow = in_array($toStatus, $expectedTargets, true);

            expect($fromStatus->canTransitionTo($toStatus))->toBe($shouldAllow);
        }
    }
});

it('returns status metadata for labels, colors, and descriptions', function () {
    expect(ProposalStatus::DRAFT->label())->toBe('Draft');
    expect(ProposalStatus::SUBMITTED->label())->toBe('Diajukan');
    expect(ProposalStatus::COMPLETED->color())->toBe('success');
    expect(ProposalStatus::REJECTED->color())->toBe('danger');
    expect(ProposalStatus::UNDER_REVIEW->description())->toContain('proses review');
});

it('marks review phase, final state, and editable state correctly', function () {
    expect(ProposalStatus::WAITING_REVIEWER->isInReviewPhase())->toBeTrue();
    expect(ProposalStatus::UNDER_REVIEW->isInReviewPhase())->toBeTrue();
    expect(ProposalStatus::REVIEWED->isInReviewPhase())->toBeTrue();
    expect(ProposalStatus::SUBMITTED->isInReviewPhase())->toBeFalse();

    expect(ProposalStatus::COMPLETED->isFinal())->toBeTrue();
    expect(ProposalStatus::REJECTED->isFinal())->toBeTrue();
    expect(ProposalStatus::REVISION_NEEDED->isFinal())->toBeFalse();

    expect(ProposalStatus::DRAFT->canEdit())->toBeTrue();
    expect(ProposalStatus::REVISION_NEEDED->canEdit())->toBeTrue();
    expect(ProposalStatus::APPROVED->canEdit())->toBeFalse();
});

it('provides complete filter options and values', function () {
    $filterOptions = ProposalStatus::filterOptions();

    expect($filterOptions['all'])->toBe('Semua Status');

    foreach (ProposalStatus::cases() as $status) {
        expect($filterOptions)->toHaveKey($status->value);
        expect($filterOptions[$status->value])->toBe($status->label());
    }

    expect(ProposalStatus::values())->toBe(array_map(
        static fn (ProposalStatus $status): string => $status->value,
        ProposalStatus::cases()
    ));
});
