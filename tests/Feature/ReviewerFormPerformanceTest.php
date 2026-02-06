<?php

use App\Livewire\Research\Proposal\ReviewerForm;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Models\ReviewCriteria;
use App\Models\ReviewLog;
use App\Models\ReviewScore;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

test('reviewer form is optimized', function () {
    // Setup data
    $user = User::factory()->create();
    $reviewer = User::factory()->create();

    // Create criteria
    $criteria1 = ReviewCriteria::create(['criteria' => 'C1', 'type' => 'research', 'is_active' => true, 'order' => 1, 'weight' => 50]);
    $criteria2 = ReviewCriteria::create(['criteria' => 'C2', 'type' => 'research', 'is_active' => true, 'order' => 2, 'weight' => 50]);

    // Create proposal
    $proposal = Proposal::factory()->create();

    // Create assignment
    $assignment = ProposalReviewer::create([
        'proposal_id' => $proposal->id,
        'user_id' => $reviewer->id,
        'status' => 'completed',
        'round' => 10,
        'assigned_at' => now(),
        'started_at' => now(),
        'completed_at' => now(),
    ]);

    // Create 10 rounds of logs for this reviewer
    for ($i = 1; $i <= 10; $i++) {
        $log = ReviewLog::create([
            'proposal_reviewer_id' => $assignment->id,
            'proposal_id' => $proposal->id,
            'user_id' => $reviewer->id,
            'round' => $i,
            'review_notes' => "Notes round $i",
            'recommendation' => 'approved',
            'total_score' => 400,
            'started_at' => now()->subDays(10 - $i),
            'completed_at' => now()->subDays(10 - $i),
        ]);

        // Create scores for each criteria
        ReviewScore::create([
            'proposal_reviewer_id' => $assignment->id,
            'review_criteria_id' => $criteria1->id,
            'acuan' => 'Good',
            'score' => 4,
            'weight_snapshot' => 50,
            'value' => 200,
            'round' => $i,
        ]);
        ReviewScore::create([
            'proposal_reviewer_id' => $assignment->id,
            'review_criteria_id' => $criteria2->id,
            'acuan' => 'Excellent',
            'score' => 5,
            'weight_snapshot' => 50,
            'value' => 250,
            'round' => $i,
        ]);
    }

    $this->actingAs($reviewer);

    DB::enableQueryLog();

    Livewire::test(ReviewerForm::class, ['proposalId' => $proposal->id])
        ->assertOk();

    $queries = DB::getQueryLog();

    $scoreQueries = collect($queries)->filter(function ($query) {
        return str_contains($query['query'], 'review_scores');
    });

    // With optimization, we expect very few queries regardless of N
    // 1 for logs, 1 for scores (eager load), 1 for criteria (nested eager load)
    // There might be some other overhead, but it should not depend on N (10).

    expect($scoreQueries->count())->toBeLessThan(5);
});
