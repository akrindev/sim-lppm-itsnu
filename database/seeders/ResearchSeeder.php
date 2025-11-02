<?php

namespace Database\Seeders;

use App\Enums\ProposalStatus;
use Illuminate\Database\Seeder;

class ResearchSeeder extends Seeder
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
        $topics = \App\Models\Topic::all();
        $nationalPriorities = \App\Models\NationalPriority::all();
        $scienceClusters = \App\Models\ScienceCluster::where('level', 1)->get();

        if ($dosenUsers->count() < 2 || $keywords->isEmpty()) {
            return;
        }

        $researchTitles = [
            'Pengembangan Sistem Manajemen Informasi Akademik Berbasis Cloud',
            'Analisis dan Optimasi Performa Database dengan Machine Learning',
            'Implementasi IoT untuk Monitoring Energi Terbarukan',
            'Sistem Rekomendasi Produk Menggunakan Collaborative Filtering',
            'Keamanan Siber dalam Transaksi E-Commerce',
            'Aplikasi Mobile untuk Ketahanan Pangan Masyarakat',
            'Teknologi Blockchain untuk Supply Chain Tracking',
            'Chatbot AI untuk Layanan Publik',
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

        // Create 8 Research Proposals
        foreach (range(0, 7) as $i) {
            $submitter = $dosenUsers->random();
            $focusArea = $focusAreas->random();
            $theme = $themes->where('focus_area_id', $focusArea->id)->first() ?? $themes->random();
            $status = $workflowStatuses[$i % count($workflowStatuses)];

            // Create Research detail first
            $research = \App\Models\Research::factory()->create();

            // Create proposal with Research
            $proposal = \App\Models\Proposal::factory()->create([
                'title' => $researchTitles[$i] ?? fake()->sentence(4),
                'detailable_type' => \App\Models\Research::class,
                'detailable_id' => $research->id,
                'submitter_id' => $submitter->id,
                'research_scheme_id' => $researchSchemes->random()->id,
                'focus_area_id' => $focusArea->id,
                'theme_id' => $theme->id,
                'topic_id' => $topics->where('theme_id', $theme->id)->first()?->id ?? $topics->random()->id,
                'national_priority_id' => $nationalPriorities->isNotEmpty() ? $nationalPriorities->random()->id : null,
                'cluster_level1_id' => $scienceClusters->isNotEmpty() ? $scienceClusters->random()->id : null,
                'status' => $status,
                'duration_in_years' => rand(1, 3),
                'sbk_value' => rand(50, 300) * 1000000, // 50-300 juta
                'summary' => fake()->paragraph(),
            ]);

            // Attach keywords (3-5 per proposal)
            if ($keywords->isNotEmpty()) {
                $proposal->keywords()->attach(
                    $keywords->random(min(rand(3, 5), $keywords->count()))->pluck('id')
                );
            }

            // Attach team members (only dosen, 2-4 anggota)
            // Team member status: accepted for submitted+ statuses, pending for draft
            $teamMemberStatus = in_array($status, [ProposalStatus::DRAFT->value, ProposalStatus::NEED_ASSIGNMENT->value])
                ? 'pending'
                : 'accepted';

            $availableMembers = $dosenUsers->where('id', '!=', $submitter->id)->random(min(rand(2, 4), $dosenUsers->count() - 1));

            foreach ($availableMembers as $member) {
                $proposal->teamMembers()->attach($member->id, [
                    'role' => 'anggota',
                    'status' => $teamMemberStatus,
                    'tasks' => fake()->sentence(10),
                ]);
            }

            // Create related data
            \App\Models\ProposalOutput::factory(rand(2, 4))->create(['proposal_id' => $proposal->id]);
            \App\Models\BudgetItem::factory(rand(5, 8))->create(['proposal_id' => $proposal->id]);
            \App\Models\ActivitySchedule::factory(rand(6, 12))->create(['proposal_id' => $proposal->id]);

            // Create research stages with dosen as person in charge
            if ($availableMembers->isNotEmpty()) {
                \App\Models\ResearchStage::factory(rand(2, 4))->create([
                    'proposal_id' => $proposal->id,
                    'person_in_charge_id' => $availableMembers->random()->id,
                ]);
            }

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
