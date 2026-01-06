<?php

namespace Database\Seeders;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use App\Models\ProposalStatusLog;
use App\Models\Research;
use App\Models\User;
use Illuminate\Database\Seeder;

class ResearchSeeder extends Seeder
{
    public function run(): void
    {
        // Retrieve all dosen users by role
        $dosenUsers = User::role('dosen')->get();

        if ($dosenUsers->count() < 2) {
            $this->command->warn('Tidak cukup dosen untuk membuat proposal penelitian');

            return;
        }

        $keywords = \App\Models\Keyword::all();
        $researchSchemes = \App\Models\ResearchScheme::all();
        $focusAreas = \App\Models\FocusArea::all();
        $themes = \App\Models\Theme::all();
        $topics = \App\Models\Topic::all();
        $nationalPriorities = \App\Models\NationalPriority::all();
        $scienceClusters = \App\Models\ScienceCluster::where('level', 1)->get();

        if ($keywords->isEmpty() || $researchSchemes->isEmpty() || $focusAreas->isEmpty()) {
            $this->command->warn('Master data tidak lengkap untuk membuat proposal');

            return;
        }

        // Indonesian research titles organized by category
        $researchTitles = [
            'Artificial Intelligence & Machine Learning' => [
                'Pengembangan Model Machine Learning untuk Prediksi Penyakit Berbasis Data Klinis',
                'Sistem Rekomendasi Produk Menggunakan Deep Learning dan Collaborative Filtering',
                'Analisis Sentimen Media Sosial dengan Natural Language Processing untuk Monitoring Merek',
                'Deteksi Fraud E-Commerce Menggunakan Ensemble Machine Learning Methods',
            ],
            'Cloud Computing & IoT' => [
                'Implementasi IoT untuk Monitoring Efisiensi Energi Terbarukan di Smart Grid',
                'Arsitektur Sistem Manajemen Informasi Akademik Berbasis Cloud Computing Microservices',
                'Platform IoT Terdistribusi untuk Precision Agriculture di Indonesia',
                'Sistem Monitoring Kualitas Udara Real-time Menggunakan Sensor Jaringan IoT',
            ],
            'Cybersecurity & Blockchain' => [
                'Teknologi Blockchain untuk Transparansi Supply Chain Produk Halal Indonesia',
                'Sistem Keamanan Multi-Layer pada Transaksi E-Commerce Menggunakan Cryptography Modern',
                'Smart Contract untuk Verifikasi Kepemilikan Aset Digital di Sektor Keuangan',
                'Analisis Vulnerabilitas dan Protokol Keamanan Sistem Informasi Publik',
            ],
            'Data Science & Analytics' => [
                'Analisis Big Data untuk Optimasi Performa Database dengan Query Optimization Techniques',
                'Sistem Business Intelligence untuk Prediksi Tren Pasar Saham Indonesia',
                'Data Mining pada Log Server untuk Deteksi Anomali Sistem Informasi',
                'Analisis Pola Migrasi Penduduk Menggunakan Spatial Data Analysis',
            ],
            'Software Engineering' => [
                'Metodologi DevOps untuk Continuous Integration/Continuous Deployment di Perusahaan Startup',
                'Testing Automation Framework untuk Meningkatkan Kualitas Software Development',
                'Refactoring Legacy Code dengan Design Patterns Modern',
                'Sistem Versioning Terdistribusi untuk Collaborative Development Teams',
            ],
        ];

        // Flatten titles array
        $flatTitles = array_reduce($researchTitles, fn ($carry, $category) => array_merge($carry, $category), []);

        // Valid initial statuses (exclude REJECTED, REVISION_NEEDED, NEED_ASSIGNMENT)
        $validStatuses = [
            ProposalStatus::DRAFT,
            ProposalStatus::SUBMITTED,
            ProposalStatus::APPROVED,
            ProposalStatus::UNDER_REVIEW,
            ProposalStatus::REVIEWED,
            ProposalStatus::COMPLETED,
        ];
        $titleIndex = 0;

        // For each dosen, create proposals covering valid statuses
        foreach ($dosenUsers as $dosenIndex => $submitter) {
            foreach ($validStatuses as $statusEnum) {
                // Create 2 proposals for each status
                for ($proposalCount = 0; $proposalCount < 2; $proposalCount++) {
                    // Select focus area and related data
                    $focusArea = $focusAreas->random();
                    $theme = $themes->where('focus_area_id', $focusArea->id)->first() ?? $themes->random();
                    $topic = $topics->where('theme_id', $theme->id)->first() ?? $topics->random();

                    // Select a valid hierarchical science cluster
                    $cluster3 = \App\Models\ScienceCluster::where('level', 3)->inRandomOrder()->first();
                    $cluster2 = $cluster3 ? \App\Models\ScienceCluster::find($cluster3->parent_id) : null;
                    $cluster1 = $cluster2 ? \App\Models\ScienceCluster::find($cluster2->parent_id) : null;

                    // Create Research detail first
                    $research = Research::factory()->create();

                    // Create title (cycle through available titles)
                    $title = $flatTitles[$titleIndex % count($flatTitles)];
                    $titleIndex++;

                    // Create Proposal with Research polymorphic relationship
                    $proposal = Proposal::factory()->create([
                        'title' => $title,
                        'detailable_type' => Research::class,
                        'detailable_id' => $research->id,
                        'submitter_id' => $submitter->id,
                        'research_scheme_id' => $researchSchemes->random()->id,
                        'focus_area_id' => $focusArea->id,
                        'theme_id' => $theme->id,
                        'topic_id' => $topic->id,
                        'national_priority_id' => $nationalPriorities->isNotEmpty() ? $nationalPriorities->random()->id : null,
                        'cluster_level1_id' => $cluster1?->id,
                        'cluster_level2_id' => $cluster2?->id,
                        'cluster_level3_id' => $cluster3?->id,
                        'status' => $statusEnum,
                        'duration_in_years' => rand(1, 3),
                        'start_year' => (int) date('Y'),
                        'sbk_value' => rand(50, 300) * 1000000, // 50-300 juta
                        'summary' => fake()->paragraph(3),
                    ]);

                    // Create ProposalStatusLog entry for initial status
                    ProposalStatusLog::create([
                        'proposal_id' => $proposal->id,
                        'user_id' => $submitter->id,
                        'status_before' => ProposalStatus::DRAFT,
                        'status_after' => $statusEnum,
                        'at' => $proposal->created_at,
                    ]);

                    // Attach keywords (3-5 per proposal)
                    if ($keywords->isNotEmpty()) {
                        $proposal->keywords()->attach(
                            $keywords->random(min(rand(3, 5), $keywords->count()))->pluck('id')
                        );
                    }

                    // Attach team members (anggota are also dosen, 2-4 per proposal)
                    $teamMemberStatus = in_array($statusEnum, [ProposalStatus::DRAFT, ProposalStatus::SUBMITTED])
                        ? 'pending'
                        : 'accepted';

                    $availableMembers = $dosenUsers
                        ->where('id', '!=', $submitter->id)
                        ->random(min(rand(2, 4), $dosenUsers->count() - 1));

                    foreach ($availableMembers as $member) {
                        $proposal->teamMembers()->attach($member->id, [
                            'role' => 'anggota',
                            'status' => $teamMemberStatus,
                            'tasks' => fake()->sentence(10),
                        ]);
                    }

                    // Create related data
                    // Mandatory outputs (Luaran Wajib)
                    \App\Models\ProposalOutput::factory(rand(1, 2))->create([
                        'proposal_id' => $proposal->id,
                        'category' => 'Wajib',
                        'output_year' => rand(1, $proposal->duration_in_years),
                    ]);

                    // Additional outputs (Luaran Tambahan)
                    \App\Models\ProposalOutput::factory(rand(1, 2))->create([
                        'proposal_id' => $proposal->id,
                        'category' => 'Tambahan',
                        'output_year' => rand(1, $proposal->duration_in_years),
                    ]);

                    \App\Models\BudgetItem::factory(rand(5, 8))->create([
                        'proposal_id' => $proposal->id,
                        'year' => rand(1, $proposal->duration_in_years),
                    ]);
                    \App\Models\ActivitySchedule::factory(rand(6, 12))->create([
                        'proposal_id' => $proposal->id,
                        'year' => rand(1, $proposal->duration_in_years),
                    ]);

                    // Create research stages with team members as person in charge
                    if ($availableMembers->isNotEmpty()) {
                        \App\Models\ResearchStage::factory(rand(2, 4))->create([
                            'proposal_id' => $proposal->id,
                            'person_in_charge_id' => $availableMembers->random()->id,
                        ]);
                    }

                    // Create reviewers for UNDER_REVIEW and REVIEWED statuses
                    if (in_array($statusEnum, [ProposalStatus::UNDER_REVIEW, ProposalStatus::REVIEWED])) {
                        $excludedIds = $availableMembers->pluck('id')->push($submitter->id)->toArray();
                        $potentialReviewers = $dosenUsers->whereNotIn('id', $excludedIds);

                        if ($potentialReviewers->isNotEmpty()) {
                            $reviewers = $potentialReviewers->random(min(2, $potentialReviewers->count()));

                            foreach ($reviewers as $reviewer) {
                                \App\Models\ProposalReviewer::create([
                                    'proposal_id' => $proposal->id,
                                    'user_id' => $reviewer->id,
                                    'status' => $statusEnum === ProposalStatus::REVIEWED ? 'completed' : 'reviewing',
                                    'review_notes' => $statusEnum === ProposalStatus::REVIEWED ? fake()->paragraph() : null,
                                    'recommendation' => $statusEnum === ProposalStatus::REVIEWED
                                        ? fake()->randomElement(['approved', 'revision_needed'])
                                        : null,
                                ]);
                            }
                        }
                    }

                    // $this->command->line("✓ Proposal penelitian dibuat: {$proposal->title} (Status: {$statusEnum->label()})");
                }
            }
        }

        $totalResearchProposals = Proposal::where('detailable_type', Research::class)->count();
        $this->command->info("Total proposal penelitian berhasil dibuat: {$totalResearchProposals}");
    }
}
