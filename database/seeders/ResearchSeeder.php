<?php

namespace Database\Seeders;

use App\Enums\ProposalStatus;
use App\Models\DailyNote;
use App\Models\MandatoryOutput;
use App\Models\ProgressReport;
use App\Models\Proposal;
use App\Models\ProposalReviewer;
use App\Models\ProposalStatusLog;
use App\Models\Research;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ResearchSeeder extends Seeder
{
    public function run(): void
    {
        // Retrieve all necessary roles
        $dosenUsers = User::role('dosen')->get();
        $reviewerUsers = User::role('reviewer')->get();
        $dekanUsers = User::role('dekan')->get();
        $kepalaLppm = User::role('kepala lppm')->first();
        $adminLppm = User::role('admin lppm')->first();

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

        if ($keywords->isEmpty() || $researchSchemes->isEmpty() || $focusAreas->isEmpty()) {
            $this->command->warn('Master data tidak lengkap untuk membuat proposal');
            return;
        }

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

        $flatTitles = array_reduce($researchTitles, fn ($carry, $category) => array_merge($carry, $category), []);

        $validStatuses = [
            ProposalStatus::DRAFT,
            ProposalStatus::SUBMITTED,
            ProposalStatus::APPROVED,
            ProposalStatus::WAITING_REVIEWER,
            ProposalStatus::UNDER_REVIEW,
            ProposalStatus::REVIEWED,
            ProposalStatus::REVISION_NEEDED,
            ProposalStatus::COMPLETED,
            ProposalStatus::REJECTED,
        ];

        $titleIndex = 0;

        foreach ($dosenUsers->take(5) as $submitter) {
            foreach ($validStatuses as $statusEnum) {
                // Determine TKT based on scheme strata
                $scheme = $researchSchemes->random();
                $tktTarget = match ($scheme->strata) {
                    'Dasar' => rand(1, 3),
                    'Terapan' => rand(4, 6),
                    'Pengembangan' => rand(7, 9),
                    default => rand(1, 3)
                };

                $focusArea = $focusAreas->random();
                $theme = $themes->where('focus_area_id', $focusArea->id)->first() ?? $themes->random();
                $topic = $topics->where('theme_id', $theme->id)->first() ?? $topics->random();

                $cluster3 = \App\Models\ScienceCluster::where('level', 3)->inRandomOrder()->first();
                $cluster2 = $cluster3 ? \App\Models\ScienceCluster::find($cluster3->parent_id) : null;
                $cluster1 = $cluster2 ? \App\Models\ScienceCluster::find($cluster2->parent_id) : null;

                // Base Date: 40 days ago
                $baseCreatedAt = Carbon::now()->subDays(40)->addHours(rand(1, 23));

                $research = Research::factory()->create([
                    'macro_research_group_id' => \App\Models\MacroResearchGroup::inRandomOrder()->first()?->id,
                    'created_at' => $baseCreatedAt,
                    'updated_at' => $baseCreatedAt,
                ]);

                // Attach TKT Level
                $tktLevel = \App\Models\TktLevel::where('level', $tktTarget)->first();
                if ($tktLevel) {
                    $research->tktLevels()->attach($tktLevel->id, ['percentage' => 100]);
                }

                $title = $flatTitles[$titleIndex % count($flatTitles)];
                $titleIndex++;

                $proposal = Proposal::factory()->create([
                    'title' => $title,
                    'detailable_type' => Research::class,
                    'detailable_id' => $research->id,
                    'submitter_id' => $submitter->id,
                    'research_scheme_id' => $scheme->id,
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
                    'sbk_value' => rand(50, 300) * 1000000,
                    'summary' => fake()->paragraph(3),
                    'created_at' => $baseCreatedAt,
                    'updated_at' => $baseCreatedAt,
                ]);

                // Create Status Log History (Sequential)
                $this->createStatusLogHistory($proposal, $statusEnum, $submitter, $dekanUsers, $kepalaLppm, $adminLppm);

                // Update proposal updated_at to match last log
                $lastLog = $proposal->statusLogs()->latest('at')->first();
                if ($lastLog) {
                    $proposal->update(['updated_at' => $lastLog->at]);
                }

                // Team Members (Ketua + Members)
                $proposal->teamMembers()->attach($submitter->id, [
                    'role' => 'ketua',
                    'status' => 'accepted',
                    'tasks' => 'Penanggung jawab utama penelitian.',
                    'created_at' => $baseCreatedAt,
                    'updated_at' => $baseCreatedAt,
                ]);

                $teamMemberStatus = in_array($statusEnum, [ProposalStatus::DRAFT, ProposalStatus::SUBMITTED]) ? 'pending' : 'accepted';
                $availableMembers = $dosenUsers->where('id', '!=', $submitter->id)->random(min(rand(1, 2), $dosenUsers->count() - 1));

                foreach ($availableMembers as $member) {
                    $proposal->teamMembers()->attach($member->id, [
                        'role' => 'anggota',
                        'status' => $teamMemberStatus,
                        'tasks' => fake()->sentence(10),
                        'created_at' => $baseCreatedAt,
                        'updated_at' => $baseCreatedAt,
                    ]);
                }

                // Targets
                $mandatoryTarget = \App\Models\ProposalOutput::factory()->create([
                    'proposal_id' => $proposal->id,
                    'category' => 'Wajib',
                    'type' => $scheme->strata === 'Terapan' ? 'Purwarupa/Prototipe' : 'Jurnal Nasional Sinta 1-2',
                    'target_status' => 'Published',
                    'output_year' => $proposal->duration_in_years,
                ]);

                $additionalTarget = \App\Models\ProposalOutput::factory()->create([
                    'proposal_id' => $proposal->id,
                    'category' => 'Tambahan',
                    'type' => 'Prosiding Seminar Internasional',
                    'target_status' => 'Accepted',
                    'output_year' => 1,
                ]);

                // Reviewers & Rounds
                if (in_array($statusEnum, [
                    ProposalStatus::UNDER_REVIEW,
                    ProposalStatus::REVIEWED,
                    ProposalStatus::REVISION_NEEDED,
                    ProposalStatus::COMPLETED,
                ])) {
                    $this->seedReviewers($proposal, $statusEnum, $reviewerUsers, $submitter, $availableMembers);
                }

                // Progress Reports & Realization
                if (in_array($statusEnum, [ProposalStatus::REVIEWED, ProposalStatus::COMPLETED])) {
                    $this->seedReports($proposal, $mandatoryTarget, $submitter);
                }

                // Daily Notes (Must be after approval/completion)
                if ($statusEnum === ProposalStatus::COMPLETED) {
                    $approvalDate = $proposal->statusLogs()->where('status_after', ProposalStatus::APPROVED)->value('at') ?? $baseCreatedAt;
                    for ($i = 0; $i < 5; $i++) {
                        DailyNote::create([
                            'proposal_id' => $proposal->id,
                            'activity_date' => Carbon::parse($approvalDate)->addDays(rand(1, 20)),
                            'activity_description' => fake()->sentence(20),
                            'progress_percentage' => ($i + 1) * 20,
                        ]);
                    }
                }
            }
        }

        $this->command->info('ResearchSeeder completed successfully.');
    }

    protected function seedReviewers($proposal, $status, $reviewerUsers, $submitter, $teamMembers): void
    {
        if ($reviewerUsers->isEmpty()) return;

        $reviewers = $reviewerUsers->random(min(2, $reviewerUsers->count()));
        $round = ($status === ProposalStatus::COMPLETED) ? 2 : 1;
        
        // Find assignment date from logs
        $assignedAt = $proposal->statusLogs()->where('status_after', ProposalStatus::UNDER_REVIEW)->value('at') 
                      ?? $proposal->created_at->addDays(3);

        foreach ($reviewers as $reviewer) {
            $isCompleted = ($status !== ProposalStatus::UNDER_REVIEW);
            
            ProposalReviewer::create([
                'proposal_id' => $proposal->id,
                'user_id' => $reviewer->id,
                'status' => $isCompleted ? 'completed' : 'pending',
                'review_notes' => $isCompleted ? fake()->paragraph(3) : null,
                'recommendation' => $isCompleted ? ($status === ProposalStatus::REVISION_NEEDED ? 'revision_needed' : 'approved') : null,
                'round' => $round,
                'assigned_at' => $assignedAt,
                'started_at' => $isCompleted ? Carbon::parse($assignedAt)->addDays(1) : null,
                'completed_at' => $isCompleted ? Carbon::parse($assignedAt)->addDays(4) : null,
                'deadline_at' => Carbon::parse($assignedAt)->addDays(14),
            ]);
        }
    }

    protected function seedReports($proposal, $mandatoryTarget, $submitter): void
    {
        $completionDate = $proposal->statusLogs()->where('status_after', ProposalStatus::COMPLETED)->value('at') 
                          ?? $proposal->statusLogs()->where('status_after', ProposalStatus::REVIEWED)->value('at')
                          ?? Carbon::now();

        // Semester 1 Report (10 days after completion/review)
        $report = ProgressReport::create([
            'proposal_id' => $proposal->id,
            'reporting_year' => date('Y'),
            'reporting_period' => 'semester_1',
            'status' => 'submitted',
            'summary_update' => fake()->paragraph(2),
            'submitted_by' => $submitter->id,
            'submitted_at' => Carbon::parse($completionDate)->addDays(10),
        ]);

        // Realize Mandatory Output
        MandatoryOutput::create([
            'progress_report_id' => $report->id,
            'proposal_output_id' => $mandatoryTarget->id,
            'status_type' => 'published',
            'journal_title' => 'International Journal of AI',
            'article_title' => 'Advanced implementation of ' . $proposal->title,
            'publication_year' => date('Y'),
            'volume' => '12',
            'issue_number' => '3',
            'doi' => '10.1234/ai.' . rand(100, 999),
        ]);

        if ($proposal->status === ProposalStatus::COMPLETED) {
            // Final Report (20 days after completion)
            ProgressReport::create([
                'proposal_id' => $proposal->id,
                'reporting_year' => date('Y'),
                'reporting_period' => 'final',
                'status' => 'submitted',
                'summary_update' => 'Penelitian telah diselesaikan dengan hasil memuaskan.',
                'submitted_by' => $submitter->id,
                'submitted_at' => Carbon::parse($completionDate)->addDays(20),
            ]);
        }
    }

    protected function createStatusLogHistory($proposal, $finalStatus, $submitter, $dekanUsers, $kepalaLppm, $adminLppm): void
    {
        $baseTime = Carbon::parse($proposal->created_at);
        $facultyId = $submitter->identity?->faculty_id;
        $dekan = $dekanUsers->first(fn($u) => $u->identity?->faculty_id === $facultyId) ?? $dekanUsers->first();

        $path = match ($finalStatus) {
            ProposalStatus::DRAFT => [],
            ProposalStatus::SUBMITTED => [
                ['f' => ProposalStatus::DRAFT, 't' => ProposalStatus::SUBMITTED, 'u' => $submitter, 'd' => 0]
            ],
            ProposalStatus::APPROVED => [
                ['f' => ProposalStatus::DRAFT, 't' => ProposalStatus::SUBMITTED, 'u' => $submitter, 'd' => 0],
                ['f' => ProposalStatus::SUBMITTED, 't' => ProposalStatus::APPROVED, 'u' => $dekan, 'd' => 2]
            ],
            ProposalStatus::WAITING_REVIEWER => [
                ['f' => ProposalStatus::DRAFT, 't' => ProposalStatus::SUBMITTED, 'u' => $submitter, 'd' => 0],
                ['f' => ProposalStatus::SUBMITTED, 't' => ProposalStatus::APPROVED, 'u' => $dekan, 'd' => 2],
                ['f' => ProposalStatus::APPROVED, 't' => ProposalStatus::WAITING_REVIEWER, 'u' => $kepalaLppm, 'd' => 4]
            ],
            ProposalStatus::UNDER_REVIEW => [
                ['f' => ProposalStatus::DRAFT, 't' => ProposalStatus::SUBMITTED, 'u' => $submitter, 'd' => 0],
                ['f' => ProposalStatus::SUBMITTED, 't' => ProposalStatus::APPROVED, 'u' => $dekan, 'd' => 2],
                ['f' => ProposalStatus::APPROVED, 't' => ProposalStatus::WAITING_REVIEWER, 'u' => $kepalaLppm, 'd' => 4],
                ['f' => ProposalStatus::WAITING_REVIEWER, 't' => ProposalStatus::UNDER_REVIEW, 'u' => $adminLppm, 'd' => 5]
            ],
            ProposalStatus::REVIEWED => [
                ['f' => ProposalStatus::DRAFT, 't' => ProposalStatus::SUBMITTED, 'u' => $submitter, 'd' => 0],
                ['f' => ProposalStatus::SUBMITTED, 't' => ProposalStatus::APPROVED, 'u' => $dekan, 'd' => 2],
                ['f' => ProposalStatus::APPROVED, 't' => ProposalStatus::WAITING_REVIEWER, 'u' => $kepalaLppm, 'd' => 4],
                ['f' => ProposalStatus::WAITING_REVIEWER, 't' => ProposalStatus::UNDER_REVIEW, 'u' => $adminLppm, 'd' => 5],
                ['f' => ProposalStatus::UNDER_REVIEW, 't' => ProposalStatus::REVIEWED, 'u' => $adminLppm, 'd' => 15]
            ],
            ProposalStatus::REVISION_NEEDED => [
                ['f' => ProposalStatus::DRAFT, 't' => ProposalStatus::SUBMITTED, 'u' => $submitter, 'd' => 0],
                ['f' => ProposalStatus::SUBMITTED, 't' => ProposalStatus::APPROVED, 'u' => $dekan, 'd' => 2],
                ['f' => ProposalStatus::APPROVED, 't' => ProposalStatus::WAITING_REVIEWER, 'u' => $kepalaLppm, 'd' => 4],
                ['f' => ProposalStatus::WAITING_REVIEWER, 't' => ProposalStatus::UNDER_REVIEW, 'u' => $adminLppm, 'd' => 5],
                ['f' => ProposalStatus::UNDER_REVIEW, 't' => ProposalStatus::REVIEWED, 'u' => $adminLppm, 'd' => 15],
                ['f' => ProposalStatus::REVIEWED, 't' => ProposalStatus::REVISION_NEEDED, 'u' => $kepalaLppm, 'd' => 16]
            ],
            ProposalStatus::COMPLETED => [
                ['f' => ProposalStatus::DRAFT, 't' => ProposalStatus::SUBMITTED, 'u' => $submitter, 'd' => 0],
                ['f' => ProposalStatus::SUBMITTED, 't' => ProposalStatus::APPROVED, 'u' => $dekan, 'd' => 2],
                ['f' => ProposalStatus::APPROVED, 't' => ProposalStatus::WAITING_REVIEWER, 'u' => $kepalaLppm, 'd' => 4],
                ['f' => ProposalStatus::WAITING_REVIEWER, 't' => ProposalStatus::UNDER_REVIEW, 'u' => $adminLppm, 'd' => 5],
                ['f' => ProposalStatus::UNDER_REVIEW, 't' => ProposalStatus::REVIEWED, 'u' => $adminLppm, 'd' => 15],
                ['f' => ProposalStatus::REVIEWED, 't' => ProposalStatus::COMPLETED, 'u' => $kepalaLppm, 'd' => 18]
            ],
            ProposalStatus::REJECTED => [
                ['f' => ProposalStatus::DRAFT, 't' => ProposalStatus::SUBMITTED, 'u' => $submitter, 'd' => 0],
                ['f' => ProposalStatus::SUBMITTED, 't' => ProposalStatus::REJECTED, 'u' => $dekan, 'd' => 2]
            ],
        };

        foreach ($path as $step) {
            ProposalStatusLog::create([
                'proposal_id' => $proposal->id,
                'user_id' => $step['u']?->id ?? $adminLppm->id,
                'status_before' => $step['f'],
                'status_after' => $step['t'],
                'at' => $baseTime->copy()->addDays($step['d']),
            ]);
        }
    }
}
