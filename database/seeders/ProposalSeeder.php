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
        $dosenUsers = \App\Models\User::with('identity')->whereHas('identity', fn ($q) => $q->where('type', 'dosen'))->get();
        $mahasiswaUsers = \App\Models\User::with('identity')->whereHas('identity', fn ($q) => $q->where('type', 'mahasiswa'))->get();
        $keywords = \App\Models\Keyword::all();
        $researchSchemes = \App\Models\ResearchScheme::all();
        $focusAreas = \App\Models\FocusArea::all();
        $themes = \App\Models\Theme::all();
        $topics = \App\Models\Topic::all();
        $nationalPriorities = \App\Models\NationalPriority::all();
        $scienceClusters = \App\Models\ScienceCluster::where('level', 1)->get();

        if ($dosenUsers->isEmpty() || $keywords->isEmpty()) {
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

        $communityServiceTitles = [
            'Pelatihan Digital Marketing untuk UMKM',
            'Program Literasi Digital Masyarakat Desa',
            'Workshop Cybersecurity untuk Pemerintah Lokal',
            'Pendampingan Teknologi untuk Sektor Pertanian',
            'Pelatihan Coding untuk Anak-anak Kurang Mampu',
        ];

        // Create 8 Research Proposals
        foreach (range(0, 7) as $i) {
            $research = \App\Models\Research::factory()->create();
            $submitter = $dosenUsers->random();
            $focusArea = $focusAreas->random();
            $theme = $themes->where('focus_area_id', $focusArea->id)->first() ?? $themes->random();

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
                'status' => fake()->randomElement(['draft', 'submitted', 'reviewed', 'approved', 'rejected', 'completed']),
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

            // Attach team members (2-4 additional members)
            $availableMembers = $dosenUsers->where('id', '!=', $submitter->id)
                ->concat($mahasiswaUsers)->random(rand(2, 4));

            foreach ($availableMembers as $member) {
                $proposal->teamMembers()->attach($member->id, [
                    'role' => fake()->randomElement(['ketua', 'anggota']),
                ]);
            }

            // Create related data
            \App\Models\ProposalOutput::factory(rand(2, 4))->create(['proposal_id' => $proposal->id]);
            \App\Models\BudgetItem::factory(rand(5, 8))->create(['proposal_id' => $proposal->id]);
            \App\Models\ActivitySchedule::factory(rand(6, 12))->create(['proposal_id' => $proposal->id]);
            \App\Models\ResearchStage::factory(rand(2, 4))->create(['proposal_id' => $proposal->id, 'person_in_charge_id' => $availableMembers->random()->id]);
        }

        // Create 5 Community Service Proposals
        foreach (range(0, 4) as $i) {
            $partner = \App\Models\Partner::inRandomOrder()->first() ?? \App\Models\Partner::factory()->create();
            $communityService = \App\Models\CommunityService::factory()->create([
                'partner_id' => $partner->id,
            ]);

            $submitter = $dosenUsers->random();
            $focusArea = $focusAreas->random();
            $theme = $themes->where('focus_area_id', $focusArea->id)->first() ?? $themes->random();

            $proposal = \App\Models\Proposal::factory()->create([
                'title' => $communityServiceTitles[$i] ?? fake()->sentence(3),
                'detailable_type' => \App\Models\CommunityService::class,
                'detailable_id' => $communityService->id,
                'submitter_id' => $submitter->id,
                'research_scheme_id' => $researchSchemes->random()->id,
                'focus_area_id' => $focusArea->id,
                'theme_id' => $theme->id,
                'status' => fake()->randomElement(['submitted', 'approved', 'reviewed', 'completed']),
                'duration_in_years' => rand(1, 2),
                'sbk_value' => rand(20, 150) * 1000000, // 20-150 juta
                'summary' => fake()->paragraph(),
            ]);

            // Attach keywords
            if ($keywords->isNotEmpty()) {
                $proposal->keywords()->attach(
                    $keywords->random(min(rand(2, 4), $keywords->count()))->pluck('id')
                );
            }

            // Attach team members
            $availableMembers = $dosenUsers->where('id', '!=', $submitter->id)
                ->concat($mahasiswaUsers)->random(rand(3, 5));

            foreach ($availableMembers as $member) {
                $proposal->teamMembers()->attach($member->id, [
                    'role' => fake()->randomElement(['ketua', 'anggota']),
                ]);
            }

            // Create related data
            \App\Models\ProposalOutput::factory(rand(1, 3))->create(['proposal_id' => $proposal->id]);
            \App\Models\BudgetItem::factory(rand(4, 7))->create(['proposal_id' => $proposal->id]);
            \App\Models\ActivitySchedule::factory(rand(8, 15))->create(['proposal_id' => $proposal->id]);
        }
    }
}
