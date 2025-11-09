<?php

namespace Database\Seeders;

use App\Enums\ProposalStatus;
use App\Models\CommunityService;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommunityServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Retrieve all dosen users by role
        $dosenUsers = User::role('dosen')->get();

        if ($dosenUsers->count() < 2) {
            $this->command->warn('Tidak cukup dosen untuk membuat proposal pengabdian masyarakat');

            return;
        }

        $keywords = \App\Models\Keyword::all();
        $researchSchemes = \App\Models\ResearchScheme::all();
        $focusAreas = \App\Models\FocusArea::all();
        $themes = \App\Models\Theme::all();
        $partners = \App\Models\Partner::all();

        if ($keywords->isEmpty() || $researchSchemes->isEmpty() || $focusAreas->isEmpty()) {
            $this->command->warn('Master data tidak lengkap untuk membuat proposal pengabdian');

            return;
        }

        // Indonesian PKM (Pengabdian Kepada Masyarakat) titles organized by category
        $pkamTitles = [
            'Digital Transformation' => [
                'Pengabdian Masyarakat: Program Literasi Digital untuk UMKM di Kota Pekalongan',
                'Pelatihan Sistem Informasi Keuangan untuk Koperasi Simpan Pinjam Desa Jepara',
                'Workshop Platform E-Commerce untuk Pengrajin Batik Tradisional',
                'Program Pelatihan Digital Marketing dan Social Media untuk Pengusaha Muda',
            ],
            'Cybersecurity & IT Support' => [
                'Pengabdian Masyarakat: Workshop Cybersecurity untuk Instansi Pemerintah Lokal',
                'Pelatihan Keamanan Data dan Privacy Protection untuk ASN',
                'Pendampingan Teknis Infrastruktur IT untuk Sekolah Negeri',
                'Workshop Perlindungan Privasi Digital untuk Generasi Muda',
            ],
            'Pertanian & Lingkungan' => [
                'Pengabdian Masyarakat: Pendampingan Teknologi Precision Agriculture untuk Petani Modern',
                'Program Pelatihan Budidaya Organik Berkelanjutan untuk Petani Pekalongan',
                'Workshop Pengelolaan Limbah Pertanian Menjadi Produk Bernilai Tambah',
                'Pendampingan Sistem Irigasi Pintar untuk Optimasi Penggunaan Air',
            ],
            'Pendidikan & Pemberdayaan' => [
                'Pengabdian Masyarakat: Program Coding dan Robotika untuk Anak Kurang Mampu',
                'Pelatihan Keterampilan STEM untuk Siswa SMP di Daerah Terpencil',
                'Workshop Kewirausahaan dan Business Plan untuk Anak Muda Pengangguran',
                'Program Mentoring Soft Skills dan Leadership untuk Mahasiswa Kurang Mampu',
            ],
            'Kesehatan & Kesejahteraan' => [
                'Pengabdian Masyarakat: Sosialisasi Kesehatan Reproduksi di Pesantren Tradisional',
                'Program Edukasi Gizi dan Kesehatan Preventif untuk Komunitas Nelayan',
                'Pelatihan First Aid dan Disaster Management untuk Relawan Bencana',
                'Workshop Mental Health Awareness untuk Karyawan Industri Manufaktur',
            ],
        ];

        // Flatten titles array
        $flatTitles = array_reduce($pkamTitles, fn($carry, $category) => array_merge($carry, $category), []);

        // All 9 workflow statuses
        $allStatuses = ProposalStatus::cases();
        $titleIndex = 0;

        // For each dosen, create proposals covering all statuses
        foreach ($dosenUsers as $dosenIndex => $submitter) {
            foreach ($allStatuses as $statusEnum) {
                // Create 2 proposals for each status
                for ($proposalCount = 0; $proposalCount < 2; $proposalCount++) {
                    // Select focus area and related data
                    $focusArea = $focusAreas->random();
                    $theme = $themes->where('focus_area_id', $focusArea->id)->first() ?? $themes->random();

                    // Select or create partner
                    $partner = $partners->isNotEmpty()
                        ? $partners->random()
                        : \App\Models\Partner::factory()->create();

                    // Create CommunityService detail first
                    $communityService = CommunityService::factory()->create([
                        'partner_id' => $partner->id,
                    ]);

                    // Create title (cycle through available titles)
                    $title = $flatTitles[$titleIndex % count($flatTitles)];
                    $titleIndex++;

                    // Create Proposal with CommunityService polymorphic relationship
                    $proposal = Proposal::factory()->create([
                        'title' => $title,
                        'detailable_type' => CommunityService::class,
                        'detailable_id' => $communityService->id,
                        'submitter_id' => $submitter->id,
                        'research_scheme_id' => $researchSchemes->random()->id,
                        'focus_area_id' => $focusArea->id,
                        'theme_id' => $theme->id,
                        'status' => $statusEnum,
                        'duration_in_years' => rand(1, 2),
                        'sbk_value' => rand(20, 150) * 1000000, // 20-150 juta
                        'summary' => fake()->paragraph(3),
                    ]);

                    // Attach keywords (2-4 per proposal)
                    if ($keywords->isNotEmpty()) {
                        $proposal->keywords()->attach(
                            $keywords->random(min(rand(2, 4), $keywords->count()))->pluck('id')
                        );
                    }

                    // Attach team members (anggota are also dosen, 3-5 per proposal for PKM)
                    $teamMemberStatus = in_array($statusEnum->value, ['draft', 'need_assignment'])
                        ? 'pending'
                        : 'accepted';

                    $availableMembers = $dosenUsers
                        ->where('id', '!=', $submitter->id)
                        ->random(min(rand(3, 5), $dosenUsers->count() - 1));

                    foreach ($availableMembers as $member) {
                        $proposal->teamMembers()->attach($member->id, [
                            'role' => 'anggota',
                            'status' => $teamMemberStatus,
                            'tasks' => fake()->sentence(10),
                        ]);
                    }

                    // Create related data for PKM
                    // Mandatory outputs (Luaran Wajib)
                    \App\Models\ProposalOutput::factory(rand(1, 2))->create([
                        'proposal_id' => $proposal->id,
                        'category' => 'Wajib',
                        'output_year' => rand(1, 2),
                    ]);

                    // Additional outputs (Luaran Tambahan)
                    \App\Models\ProposalOutput::factory(rand(0, 2))->create([
                        'proposal_id' => $proposal->id,
                        'category' => 'Tambahan',
                        'output_year' => rand(1, 2),
                    ]);

                    \App\Models\BudgetItem::factory(rand(4, 7))->create(['proposal_id' => $proposal->id]);
                    \App\Models\ActivitySchedule::factory(rand(8, 15))->create(['proposal_id' => $proposal->id]);

                    // Create reviewers for UNDER_REVIEW and REVIEWED statuses
                    if (in_array($statusEnum->value, ['under_review', 'reviewed'])) {
                        $excludedIds = $availableMembers->pluck('id')->push($submitter->id)->toArray();
                        $potentialReviewers = $dosenUsers->whereNotIn('id', $excludedIds);

                        if ($potentialReviewers->isNotEmpty()) {
                            $reviewers = $potentialReviewers->random(min(2, $potentialReviewers->count()));

                            foreach ($reviewers as $reviewer) {
                                \App\Models\ProposalReviewer::create([
                                    'proposal_id' => $proposal->id,
                                    'user_id' => $reviewer->id,
                                    'status' => $statusEnum->value === 'reviewed' ? 'completed' : 'reviewing',
                                    'review_notes' => $statusEnum->value === 'reviewed' ? fake()->paragraph() : null,
                                    'recommendation' => $statusEnum->value === 'reviewed'
                                        ? fake()->randomElement(['approved', 'revision_needed'])
                                        : null,
                                ]);
                            }
                        }
                    }

                    // $this->command->line("✓ Proposal pengabdian masyarakat dibuat: {$proposal->title} (Status: {$statusEnum->label()})");
                }
            }
        }

        $totalPkamProposals = Proposal::where('detailable_type', CommunityService::class)->count();
        $this->command->info("Total proposal pengabdian masyarakat berhasil dibuat: {$totalPkamProposals}");
    }
}
