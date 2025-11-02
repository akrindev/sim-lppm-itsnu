<?php

namespace Database\Seeders;

use App\Enums\ProposalStatus;
use Illuminate\Database\Seeder;

class CommunityServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get necessary data
        $dosenUsers = \App\Models\User::with('identity')->whereHas('identity', fn($q) => $q->where('type', 'dosen'))->get();
        $keywords = \App\Models\Keyword::all();
        $researchSchemes = \App\Models\ResearchScheme::all();
        $focusAreas = \App\Models\FocusArea::all();
        $themes = \App\Models\Theme::all();

        if ($dosenUsers->count() < 2 || $keywords->isEmpty()) {
            return;
        }

        $communityServiceTitles = [
            'Pelatihan Digital Marketing untuk UMKM',
            'Program Literasi Digital Masyarakat Desa',
            'Workshop Cybersecurity untuk Pemerintah Lokal',
            'Pendampingan Teknologi untuk Sektor Pertanian',
            'Pelatihan Coding untuk Anak-anak Kurang Mampu',
        ];

        // Workflow statuses based on documentation
        // draft -> submitted -> approved (Dekan) -> need_assignment (Kepala LPPM initial) ->
        // under_review (reviewers assigned) -> reviewed (all reviews done) -> completed (final approval)
        $workflowStatuses = [
            ProposalStatus::DRAFT->value,
            ProposalStatus::SUBMITTED->value,
            ProposalStatus::APPROVED->value,
            ProposalStatus::NEED_ASSIGNMENT->value,
            ProposalStatus::UNDER_REVIEW->value,
            ProposalStatus::REVIEWED->value,
            ProposalStatus::COMPLETED->value,
            ProposalStatus::REVISION_NEEDED->value,
        ];

        // Create 5 Community Service Proposals
        foreach (range(0, 4) as $i) {
            $submitter = $dosenUsers->random();
            $focusArea = $focusAreas->random();
            $theme = $themes->where('focus_area_id', $focusArea->id)->first() ?? $themes->random();
            $status = $workflowStatuses[$i % count($workflowStatuses)];

            // Create Partner and CommunityService detail first
            $partner = \App\Models\Partner::inRandomOrder()->first() ?? \App\Models\Partner::factory()->create();
            $communityService = \App\Models\CommunityService::factory()->create([
                'partner_id' => $partner->id,
            ]);

            // Create proposal with CommunityService
            $proposal = \App\Models\Proposal::factory()->create([
                'title' => $communityServiceTitles[$i] ?? fake()->sentence(3),
                'detailable_type' => \App\Models\CommunityService::class,
                'detailable_id' => $communityService->id,
                'submitter_id' => $submitter->id,
                'research_scheme_id' => $researchSchemes->random()->id,
                'focus_area_id' => $focusArea->id,
                'theme_id' => $theme->id,
                'status' => $status,
                'duration_in_years' => rand(1, 2),
                'sbk_value' => rand(20, 150) * 1000000, // 20-150 juta
                'summary' => fake()->paragraph(),
            ]);

            // Attach keywords (2-4 per proposal)
            if ($keywords->isNotEmpty()) {
                $proposal->keywords()->attach(
                    $keywords->random(min(rand(2, 4), $keywords->count()))->pluck('id')
                );
            }

            // Attach team members (only dosen, 3-5 anggota)
            $teamMemberStatus = in_array($status, [ProposalStatus::DRAFT->value, ProposalStatus::NEED_ASSIGNMENT->value])
                ? 'pending'
                : 'accepted';

            $availableMembers = $dosenUsers->where('id', '!=', $submitter->id)->random(min(rand(3, 5), $dosenUsers->count() - 1));

            foreach ($availableMembers as $member) {
                $proposal->teamMembers()->attach($member->id, [
                    'role' => 'anggota',
                    'status' => $teamMemberStatus,
                    'tasks' => fake()->sentence(10),
                ]);
            }

            // Create related data
            \App\Models\ProposalOutput::factory(rand(1, 3))->create(['proposal_id' => $proposal->id]);
            \App\Models\BudgetItem::factory(rand(4, 7))->create(['proposal_id' => $proposal->id]);
            \App\Models\ActivitySchedule::factory(rand(8, 15))->create(['proposal_id' => $proposal->id]);

            // If status is under_review or reviewed, create reviewers
            if (in_array($status, [ProposalStatus::UNDER_REVIEW->value, ProposalStatus::REVIEWED->value])) {
                $reviewers = $dosenUsers->whereNotIn('id', $availableMembers->pluck('id')->push($submitter->id))
                    ->random(min(2, $dosenUsers->count() - $availableMembers->count() - 1));

                foreach ($reviewers as $reviewer) {
                    \App\Models\ProposalReviewer::create([
                        'proposal_id' => $proposal->id,
                        'user_id' => $reviewer->id,
                        'status' => $status === ProposalStatus::REVIEWED->value ? 'completed' : 'reviewing',
                        'review_notes' => $status === ProposalStatus::REVIEWED->value ? fake()->paragraph() : null,
                        'recommendation' => $status === ProposalStatus::REVIEWED->value
                            ? fake()->randomElement(['approved', 'revision_needed'])
                            : null,
                    ]);
                }
            }
        }
    }
}
