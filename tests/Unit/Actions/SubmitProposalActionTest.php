<?php

declare(strict_types=1);

use App\Enums\ProposalStatus;
use App\Livewire\Actions\SubmitProposalAction;
use App\Models\Proposal;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

use function Pest\Laravel\actingAs;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

function createProposalForSubmission(array $attributes = []): Proposal
{
    $submitter = User::factory()->create();

    return Proposal::query()->create(array_merge([
        'title' => 'Submit Proposal Action Unit Test',
        'submitter_id' => $submitter->id,
        'status' => ProposalStatus::DRAFT,
    ], $attributes));
}

function createSubmitProposalActionSpy(): array
{
    $spy = new class extends NotificationService
    {
        public int $notifyProposalSubmittedCalls = 0;

        public function notifyProposalSubmitted($proposal, $submitter, Collection|array $recipients): void
        {
            $this->notifyProposalSubmittedCalls++;
        }
    };

    return [$spy, new SubmitProposalAction($spy)];
}

it('rejects submission when team members have not accepted yet', function () {
    [$spy, $action] = createSubmitProposalActionSpy();

    $proposal = createProposalForSubmission(['status' => ProposalStatus::DRAFT]);
    $leader = $proposal->submitter;
    $member = User::factory()->create();

    $proposal->teamMembers()->attach($leader->id, ['role' => 'ketua', 'status' => 'accepted']);
    $proposal->teamMembers()->attach($member->id, ['role' => 'anggota', 'status' => 'pending']);

    $result = $action->execute($proposal);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('anggota masih belum menerima undangan');
    expect($proposal->fresh()->status)->toBe(ProposalStatus::DRAFT);
    expect($spy->notifyProposalSubmittedCalls)->toBe(0);
});

it('rejects submission from disallowed status', function () {
    [$spy, $action] = createSubmitProposalActionSpy();

    $proposal = createProposalForSubmission(['status' => ProposalStatus::APPROVED]);

    $result = $action->execute($proposal);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('tidak dapat diajukan');
    expect($proposal->fresh()->status)->toBe(ProposalStatus::APPROVED);
    expect($spy->notifyProposalSubmittedCalls)->toBe(0);
});

it('allows submission from need_assignment after team issues are resolved', function () {
    Artisan::call('db:seed', ['--class' => \Database\Seeders\RoleSeeder::class]);

    [$spy, $action] = createSubmitProposalActionSpy();

    $proposal = createProposalForSubmission(['status' => ProposalStatus::NEED_ASSIGNMENT]);
    $leader = $proposal->submitter;
    $member = User::factory()->create();

    $proposal->teamMembers()->attach($leader->id, ['role' => 'ketua', 'status' => 'accepted']);
    $proposal->teamMembers()->attach($member->id, ['role' => 'anggota', 'status' => 'accepted']);

    actingAs($leader);

    $result = $action->execute($proposal);

    expect($result['success'])->toBeTrue();
    expect($result['message'])->toContain('berhasil diajukan');
    expect($proposal->fresh()->status)->toBe(ProposalStatus::SUBMITTED);
    expect($spy->notifyProposalSubmittedCalls)->toBe(1);
});
