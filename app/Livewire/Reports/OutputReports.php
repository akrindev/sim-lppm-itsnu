<?php

namespace App\Livewire\Reports;

use App\Models\AdditionalOutput;
use App\Models\MandatoryOutput;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class OutputReports extends Component
{
    use WithPagination;

    /**
     * Active tab (research or community-service)
     */
    public string $activeTab = 'research';

    /**
     * Search query
     */
    public string $search = '';

    /**
     * Output type filter (all, mandatory, additional)
     */
    public string $outputType = 'all';

    /**
     * Reset pagination when search or filter changes
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when output type filter changes
     */
    public function updatingOutputType(): void
    {
        $this->resetPage();
    }

    /**
     * Set the active tab
     */
    public function setTab(string $tab): void
    {
        if (! in_array($tab, ['research', 'community-service'])) {
            return;
        }

        $this->activeTab = $tab;
        $this->resetPage();
    }

    /**
     * Render the component view
     */
    public function render(): View
    {
        return view('livewire.reports.output-reports', [
            'mandatoryOutputs' => $this->getMandatoryOutputs(),
            'additionalOutputs' => $this->getAdditionalOutputs(),
            'statistics' => $this->getStatistics(),
        ]);
    }

    /**
     * Get mandatory outputs based on active tab and filters
     */
    protected function getMandatoryOutputs(): Collection
    {
        if ($this->outputType === 'additional') {
            return collect();
        }

        $detailableType = $this->activeTab === 'research' ? 'App\\Models\\Research' : 'App\\Models\\CommunityService';

        $query = MandatoryOutput::query()
            ->with(['progressReport.proposal.user', 'proposalOutput'])
            ->whereHas('progressReport.proposal', function (Builder $query) use ($detailableType) {
                $query->where('detailable_type', $detailableType);
            });

        // Apply search filter
        if ($this->search) {
            $query->where(function (Builder $q) {
                $q->where('journal_title', 'like', "%{$this->search}%")
                    ->orWhere('article_title', 'like', "%{$this->search}%")
                    ->orWhere('book_title', 'like', "%{$this->search}%")
                    ->orWhere('isbn', 'like', "%{$this->search}%")
                    ->orWhere('product_name', 'like', "%{$this->search}%");
            });
        }

        return $query->latest()->get();
    }

    /**
     * Get additional outputs based on active tab and filters
     */
    protected function getAdditionalOutputs(): Collection
    {
        if ($this->outputType === 'mandatory') {
            return collect();
        }

        $detailableType = $this->activeTab === 'research' ? 'App\\Models\\Research' : 'App\\Models\\CommunityService';

        $query = AdditionalOutput::query()
            ->with(['progressReport.proposal.user', 'proposalOutput'])
            ->whereHas('progressReport.proposal', function (Builder $query) use ($detailableType) {
                $query->where('detailable_type', $detailableType);
            });

        // Apply search filter
        if ($this->search) {
            $query->where(function (Builder $q) {
                $q->where('book_title', 'like', "%{$this->search}%")
                    ->orWhere('journal_title', 'like', "%{$this->search}%")
                    ->orWhere('isbn', 'like', "%{$this->search}%")
                    ->orWhere('product_name', 'like', "%{$this->search}%");
            });
        }

        return $query->latest()->get();
    }

    /**
     * Get statistics for the current tab
     */
    protected function getStatistics(): array
    {
        $detailableType = $this->activeTab === 'research' ? 'App\\Models\\Research' : 'App\\Models\\CommunityService';

        $mandatoryCount = MandatoryOutput::query()
            ->whereHas('progressReport.proposal', function (Builder $query) use ($detailableType) {
                $query->where('detailable_type', $detailableType);
            })
            ->count();

        $additionalCount = AdditionalOutput::query()
            ->whereHas('progressReport.proposal', function (Builder $query) use ($detailableType) {
                $query->where('detailable_type', $detailableType);
            })
            ->count();

        $totalProposals = Proposal::query()
            ->where('detailable_type', $detailableType)
            ->whereIn('status', ['approved', 'ongoing', 'completed'])
            ->count();

        return [
            'mandatory' => $mandatoryCount,
            'additional' => $additionalCount,
            'total' => $mandatoryCount + $additionalCount,
            'proposals' => $totalProposals,
        ];
    }

    /**
     * View a mandatory output in modal
     */
    public ?string $viewingMandatoryId = null;

    public function viewMandatoryOutput(string $id): void
    {
        $this->viewingMandatoryId = $id;
    }

    public function closeMandatoryModal(): void
    {
        $this->viewingMandatoryId = null;
    }

    public function mandatoryOutput(): ?MandatoryOutput
    {
        if (! $this->viewingMandatoryId) {
            return null;
        }

        return MandatoryOutput::with(['proposalOutput'])->find($this->viewingMandatoryId);
    }

    /**
     * View an additional output in modal
     */
    public ?string $viewingAdditionalId = null;

    public function viewAdditionalOutput(string $id): void
    {
        $this->viewingAdditionalId = $id;
    }

    public function closeAdditionalModal(): void
    {
        $this->viewingAdditionalId = null;
    }

    public function additionalOutput(): ?AdditionalOutput
    {
        if (! $this->viewingAdditionalId) {
            return null;
        }

        return AdditionalOutput::with(['proposalOutput'])->find($this->viewingAdditionalId);
    }

    /**
     * Get the display name for an output category
     */
    public function getOutputCategoryName(string $category): string
    {
        $categories = [
            'journal' => 'Jurnal',
            'book' => 'Buku',
            'hki' => 'HKI',
            'product' => 'Produk',
            'media' => 'Media Massa',
            'video' => 'Video',
        ];

        return $categories[$category] ?? ucfirst($category);
    }
}
