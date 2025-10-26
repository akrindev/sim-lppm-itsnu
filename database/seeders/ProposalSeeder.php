<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProposalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get necessary data
        $users = \App\Models\User::with('identity')->whereHas('identity', fn($q) => $q->where('type', 'dosen'))->get();
        $keywords = \App\Models\Keyword::all();

        if ($users->isEmpty()) {
            return;
        }

        // Create 5 Research Proposals
        foreach (range(1, 5) as $i) {
            $research = \App\Models\Research::factory()->create();

            $proposal = \App\Models\Proposal::factory()->create([
                'detailable_type' => \App\Models\Research::class,
                'detailable_id' => $research->id,
                'submitter_id' => $users->random()->id,
                'status' => fake()->randomElement(['draft', 'submitted', 'reviewed', 'approved']),
            ]);

            // Attach keywords (2-4 per proposal)
            $proposal->keywords()->attach(
                $keywords->random(rand(2, 4))->pluck('id')
            );

            // Attach team members (1-3 additional members)
            $teamMembers = $users->where('id', '!=', $proposal->submitter_id)
                ->random(rand(1, 3));

            foreach ($teamMembers as $member) {
                $proposal->teamMembers()->attach($member->id, [
                    'role' => fake()->randomElement(['ketua', 'anggota']),
                ]);
            }

            // Create related data
            \App\Models\ProposalOutput::factory(rand(1, 3))->create(['proposal_id' => $proposal->id]);
            \App\Models\BudgetItem::factory(rand(3, 6))->create(['proposal_id' => $proposal->id]);
            \App\Models\ActivitySchedule::factory(rand(4, 8))->create(['proposal_id' => $proposal->id]);
            \App\Models\ResearchStage::factory(rand(2, 3))->create(['proposal_id' => $proposal->id]);
        }

        // Create 3 Community Service Proposals
        foreach (range(1, 3) as $i) {
            $communityService = \App\Models\CommunityService::factory()->create();

            $proposal = \App\Models\Proposal::factory()->create([
                'detailable_type' => \App\Models\CommunityService::class,
                'detailable_id' => $communityService->id,
                'submitter_id' => $users->random()->id,
                'status' => fake()->randomElement(['submitted', 'approved', 'completed']),
            ]);

            // Attach keywords
            $proposal->keywords()->attach(
                $keywords->random(rand(2, 3))->pluck('id')
            );

            // Attach team members
            $teamMembers = $users->where('id', '!=', $proposal->submitter_id)
                ->random(rand(2, 4));

            foreach ($teamMembers as $member) {
                $proposal->teamMembers()->attach($member->id, [
                    'role' => fake()->randomElement(['ketua', 'anggota']),
                ]);
            }

            // Create related data
            \App\Models\ProposalOutput::factory(rand(1, 2))->create(['proposal_id' => $proposal->id]);
            \App\Models\BudgetItem::factory(rand(3, 5))->create(['proposal_id' => $proposal->id]);
            \App\Models\ActivitySchedule::factory(rand(5, 10))->create(['proposal_id' => $proposal->id]);
        }
    }
}
