<?php

namespace App\Livewire\Abstracts;

use App\Livewire\Forms\ProposalForm;
use App\Livewire\Traits\WithProposalWizard;
use App\Livewire\Traits\WithStepWizard;
use App\Services\BudgetValidationService;
use App\Services\MasterDataService;
use App\Services\ProposalService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

abstract class ProposalCreate extends Component
{
    use WithProposalWizard;
    use WithStepWizard;

    public ProposalForm $form;

    public int $currentStep = 1;

    public string $author_name = '';

    public array $budgetValidationErrors = [];

    public function mount(?string $proposalId = null): void
    {
        $this->author_name = Auth::user()->name;

        if ($proposalId) {
            $proposal = \App\Models\Proposal::findOrFail($proposalId);

            if (! $this->canEditProposal($proposal)) {
                abort(403);
            }

            $this->form->setProposal($proposal);
        }
    }

    protected function canEditProposal(\App\Models\Proposal $proposal): bool
    {
        return $proposal->status === 'draft'
            && $proposal->submitter_id === Auth::id();
    }

    abstract protected function getProposalType(): string;

    abstract protected function getIndexRoute(): string;

    abstract protected function getShowRoute(string $proposalId): string;

    abstract protected function getStep2Rules(): array;

    protected function getProposalTypeForValidation(): string
    {
        return $this->getProposalType();
    }

    public function updateMembers(array $members): void
    {
        $this->form->members = $members;
    }

    public function updateTktResults(array $tktResults): void
    {
        $this->form->tkt_results = $tktResults;
    }

    #[On('tkt-calculated')]
    public function onTktCalculated(array $levelResults, array $indicatorScores): void
    {
        // Only update level results with levels that have actual progress (percentage > 0)
        $filteredResults = array_filter($levelResults, fn ($data) => ($data['percentage'] ?? 0) > 0);
        $this->form->tkt_results = $filteredResults;
        $this->form->tkt_indicator_scores = $indicatorScores;
    }

    public function save(): void
    {
        $this->form->validate();

        app(BudgetValidationService::class)->validateBudgetGroupPercentages(
            $this->form->budget_items,
            $this->getProposalType()
        );

        app(BudgetValidationService::class)->validateBudgetCap(
            $this->form->budget_items,
            $this->getProposalType()
        );

        $proposal = app(ProposalService::class)->createProposal(
            $this->form,
            $this->getProposalType()
        );

        $this->redirectRoute($this->getShowRoute($proposal->id));
    }

    #[Computed]
    public function schemes()
    {
        return app(MasterDataService::class)->schemes();
    }

    #[Computed]
    public function focusAreas()
    {
        return app(MasterDataService::class)->focusAreas();
    }

    #[Computed]
    public function themes()
    {
        return app(MasterDataService::class)->themes();
    }

    #[Computed]
    public function topics()
    {
        return app(MasterDataService::class)->topics();
    }

    #[Computed]
    public function nationalPriorities()
    {
        return app(MasterDataService::class)->nationalPriorities();
    }

    #[Computed]
    public function scienceClusters()
    {
        return app(MasterDataService::class)->scienceClusters();
    }

    #[Computed]
    public function macroResearchGroups()
    {
        return app(MasterDataService::class)->macroResearchGroups();
    }

    #[Computed]
    public function partners()
    {
        return app(MasterDataService::class)->partners();
    }

    #[Computed]
    public function budgetGroups()
    {
        return app(MasterDataService::class)->budgetGroups();
    }

    #[Computed]
    public function budgetComponents()
    {
        return app(MasterDataService::class)->budgetComponents();
    }

    #[Computed]
    public function tktTypes()
    {
        return app(MasterDataService::class)->tktTypes();
    }

    #[Computed]
    public function templateUrl()
    {
        return app(MasterDataService::class)->getTemplateUrl($this->getProposalType());
    }

    protected function getStepValidationRules(int $step): array
    {
        $type = $this->getProposalType();

        return match ($step) {
            1 => [
                'form.title' => 'required|string|max:255',
                'form.research_scheme_id' => $type === 'research' ? 'required|exists:research_schemes,id' : 'nullable|exists:research_schemes,id',
                'form.focus_area_id' => 'required|exists:focus_areas,id',
                'form.theme_id' => 'required|exists:themes,id',
                'form.topic_id' => 'required|exists:topics,id',
                'form.national_priority_id' => 'nullable|exists:national_priorities,id',
                'form.cluster_level1_id' => 'required|exists:science_clusters,id',
                'form.cluster_level2_id' => 'nullable|exists:science_clusters,id',
                'form.cluster_level3_id' => 'nullable|exists:science_clusters,id',
                'form.sbk_value' => 'nullable|numeric|min:0',
                'form.duration_in_years' => 'required|integer|min:1|max:10',
                'form.start_year' => 'required|integer|min:2020|max:2050',
                'form.summary' => 'required|string|min:100',
            ],
            2 => $this->getStep2Rules(),
            3 => [
                'form.budget_items' => 'required|array|min:1',
            ],
            4 => [
                'form.partner_ids' => 'nullable|array',
            ],
            default => [],
        };
    }
}
