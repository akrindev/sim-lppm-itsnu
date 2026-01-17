<?php

namespace App\Livewire\Reports;

use App\Enums\ProposalStatus;
use App\Models\AdditionalOutput;
use App\Models\MandatoryOutput;
use App\Models\Proposal;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'Laporan PKM', 'pageTitle' => 'Laporan PKM'])]
class CommunityService extends Component
{
    public string $period;

    public function mount()
    {
        $this->period = (string) date('Y');
    }

    /**
     * Update the selected reporting period.
     */
    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        return view('livewire.reports.community-service', [
            'periods' => $this->availablePeriods(),
            'summary' => $this->summaryMetrics(),
            'schemes' => $this->pkmByScheme(),
            'focusAreas' => $this->pkmByFocusArea(),
            'faculties' => $this->pkmByFaculty(),
            'outputStats' => $this->outputAnalytics(),
            'recentPkm' => $this->recentPkm(),
        ]);
    }

    /**
     * Group PKM by focus area for the current period.
     */
    protected function pkmByFocusArea(): Collection
    {
        return Proposal::query()
            ->where('detailable_type', 'App\Models\CommunityService')
            ->where('start_year', $this->period)
            ->with('focusArea')
            ->get()
            ->groupBy('focus_area_id')
            ->map(function ($proposals) {
                $first = $proposals->first();

                return [
                    'name' => $first->focusArea->name ?? __('Lainnya'),
                    'count' => $proposals->count(),
                ];
            })
            ->sortByDesc('count');
    }

    /**
     * Get available years from proposals.
     */
    protected function availablePeriods(): array
    {
        return Proposal::query()
            ->distinct()
            ->whereNotNull('start_year')
            ->orderBy('start_year', 'desc')
            ->pluck('start_year')
            ->map(fn ($year) => (string) $year)
            ->toArray() ?: [(string) date('Y')];
    }

    /**
     * Aggregate report metrics for the dashboard cards.
     */
    protected function summaryMetrics(): array
    {
        $query = Proposal::query()
            ->where('detailable_type', 'App\Models\CommunityService')
            ->where('start_year', $this->period);

        $totalApproved = (clone $query)
            ->whereIn('status', [ProposalStatus::APPROVED, ProposalStatus::COMPLETED, ProposalStatus::WAITING_REVIEWER, ProposalStatus::UNDER_REVIEW, ProposalStatus::REVIEWED])
            ->count();

        $totalBudget = (clone $query)
            ->whereIn('status', [ProposalStatus::APPROVED, ProposalStatus::COMPLETED])
            ->sum('sbk_value');

        $reportsCount = (clone $query)
            ->whereHas('progressReports')
            ->count();

        return [
            [
                'label' => __('Proposal Disetujui'),
                'value' => $totalApproved,
                'icon' => 'check',
                'variant' => 'bg-green-lt text-green',
            ],
            [
                'label' => __('Total Anggaran'),
                'value' => 'Rp '.number_format($totalBudget, 0, ',', '.'),
                'icon' => 'currency-dollar',
                'variant' => 'bg-blue-lt text-blue',
            ],
            [
                'label' => __('Laporan Terkumpul'),
                'value' => $reportsCount,
                'icon' => 'file-text',
                'variant' => 'bg-yellow-lt text-yellow',
            ],
        ];
    }

    /**
     * Aggregate output statistics for the current period.
     */
    protected function outputAnalytics(): Collection
    {
        $proposalIds = Proposal::query()
            ->where('detailable_type', 'App\Models\CommunityService')
            ->where('start_year', $this->period)
            ->pluck('id');

        $mandatory = MandatoryOutput::query()
            ->whereHas('progressReport', fn ($q) => $q->whereIn('proposal_id', $proposalIds))
            ->with('proposalOutput')
            ->get();

        $additional = AdditionalOutput::query()
            ->whereHas('progressReport', fn ($q) => $q->whereIn('proposal_id', $proposalIds))
            ->with('proposalOutput')
            ->get();

        return $mandatory->concat($additional)
            ->groupBy(fn ($output) => $output->proposalOutput->category ?? 'Lainnya')
            ->map(fn ($group, $key) => [
                'category' => $this->translateCategory($key),
                'count' => $group->count(),
                'published' => $group->filter(fn ($o) => in_array($o->status_type ?? $o->status, ['published', 'terbit', 'granted']))->count(),
            ])
            ->sortByDesc('count');
    }

    /**
     * Simple category translation.
     */
    protected function translateCategory(string $key): string
    {
        $categories = [
            'journal' => __('Jurnal'),
            'book' => __('Buku'),
            'hki' => __('HKI'),
            'product' => __('Produk'),
            'media' => __('Media Massa'),
            'video' => __('Video'),
        ];

        return $categories[strtolower($key)] ?? ucfirst($key);
    }

    /**
     * Group PKM by scheme for the current period.
     */
    protected function pkmByScheme(): Collection
    {
        return Proposal::query()
            ->where('detailable_type', 'App\Models\CommunityService')
            ->where('start_year', $this->period)
            ->with('researchScheme')
            ->get()
            ->groupBy('research_scheme_id')
            ->map(function ($proposals) {
                $first = $proposals->first();

                return [
                    'name' => $first->researchScheme->name ?? __('Tanpa Skema'),
                    'count' => $proposals->count(),
                    'budget' => $proposals->sum('sbk_value'),
                ];
            })
            ->sortByDesc('count');
    }

    /**
     * Group PKM by faculty for the current period.
     */
    protected function pkmByFaculty(): Collection
    {
        return Proposal::query()
            ->where('detailable_type', 'App\Models\CommunityService')
            ->where('start_year', $this->period)
            ->with(['submitter.identity.faculty'])
            ->get()
            ->groupBy(fn ($p) => $p->submitter?->identity?->faculty_id)
            ->map(function ($proposals) {
                $first = $proposals->first();

                return [
                    'name' => $first->submitter?->identity?->faculty?->name ?? __('Pusat/Lainnya'),
                    'count' => $proposals->count(),
                ];
            })
            ->sortByDesc('count');
    }

    /**
     * Get recent PKM proposals for the current period.
     */
    protected function recentPkm(): Collection
    {
        return Proposal::query()
            ->where('detailable_type', 'App\Models\CommunityService')
            ->where('start_year', $this->period)
            ->with(['submitter', 'researchScheme'])
            ->latest()
            ->take(10)
            ->get();
    }
}
