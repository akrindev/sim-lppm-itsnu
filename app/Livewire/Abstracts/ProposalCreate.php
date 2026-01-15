<?php

namespace App\Livewire\Abstracts;

use App\Livewire\Concerns\HasToast;
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
use Livewire\WithFileUploads;

abstract class ProposalCreate extends Component
{
    use HasToast;
    use WithFileUploads;
    use WithProposalWizard;
    use WithStepWizard;

    public ProposalForm $form;

    public int $currentStep = 1;

    public int $fileInputIteration = 0;

    public string $author_name = '';

    public array $budgetValidationErrors = [];

    public function mount(?string $proposalId = null, ?\App\Models\Proposal $proposal = null): void
    {
        $this->author_name = Auth::user()->name;

        // Handle route model binding (if passed as object) or ID string
        $proposalToLoad = $proposal ?? ($proposalId ? \App\Models\Proposal::find($proposalId) : null);

        if ($proposalToLoad) {
            if (! $this->canEditProposal($proposalToLoad)) {
                abort(403);
            }

            $this->form->setProposal($proposalToLoad);
        } elseif ($proposalId) {
            // Fallback if find failed but ID was provided (should have been caught by model binding usually)
            abort(404);
        }
    }

    protected function canEditProposal(\App\Models\Proposal $proposal): bool
    {
        return $proposal->status === \App\Enums\ProposalStatus::DRAFT
            && $proposal->submitter_id === Auth::id();
    }

    abstract protected function getProposalType(): string;

    abstract protected function getIndexRoute(): string;

    abstract protected function getShowRoute(string $proposalId): string;

    abstract protected function getStep2Rules(): array;

    public function validationAttributes(): array
    {
        return [
            'form.title' => 'Judul',
            'form.research_scheme_id' => 'Skema Penelitian',
            'form.focus_area_id' => 'Bidang Fokus',
            'form.theme_id' => 'Tema',
            'form.topic_id' => 'Topik',
            'form.national_priority_id' => 'Prioritas Riset Nasional',
            'form.cluster_level1_id' => 'Rumpun Ilmu Level 1',
            'form.cluster_level2_id' => 'Rumpun Ilmu Level 2',
            'form.cluster_level3_id' => 'Rumpun Ilmu Level 3',
            'form.sbk_value' => 'Nilai SBK',
            'form.duration_in_years' => 'Lama Kegiatan',
            'form.start_year' => 'Tahun Usulan',
            'form.summary' => 'Ringkasan',
            'form.author_tasks' => 'Tugas Ketua',
            'form.tkt_type' => 'Jenis TKT',
            'form.macro_research_group_id' => 'Kelompok Makro Riset',
            'form.substance_file' => 'Substansi Usulan (PDF)',
            'form.outputs' => 'Luaran Target Capaian',
            'form.budget_items' => 'RAB',
            'form.partner_ids' => 'Mitra',
        ];
    }

    protected function getProposalTypeForValidation(): string
    {
        return $this->getProposalType();
    }

    #[On('members-updated')]
    public function updateMembers(array $members): void
    {
        $this->form->members = $members;
    }

    public function updatedFormFocusAreaId(): void
    {
        $this->form->theme_id = '';
        $this->form->topic_id = '';
    }

    public function updatedFormThemeId(): void
    {
        $this->form->topic_id = '';
    }

    public function updatedFormClusterLevel1Id(): void
    {
        $this->form->cluster_level2_id = '';
        $this->form->cluster_level3_id = '';
    }

    public function updatedFormClusterLevel2Id(): void
    {
        $this->form->cluster_level3_id = '';
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

        if ($this->form->proposal) {
            app(ProposalService::class)->updateProposal(
                $this->form->proposal,
                $this->form
            );
            $proposal = $this->form->proposal;
        } else {
            $proposal = app(ProposalService::class)->createProposal(
                $this->form,
                $this->getProposalType()
            );
        }

        $this->redirect($this->getShowRoute($proposal->id));
    }

    public function saveDraft(): void
    {
        // Validate only the current step
        $rules = $this->getStepValidationRules($this->currentStep);
        if (! empty($rules)) {
            $this->validate($rules);
        }

        // Additional validation for budget in step 3 if items are present
        if ($this->currentStep === 3 && ! empty($this->form->budget_items)) {
            app(BudgetValidationService::class)->validateBudgetGroupPercentages(
                $this->form->budget_items,
                $this->getProposalType()
            );

            app(BudgetValidationService::class)->validateBudgetCap(
                $this->form->budget_items,
                $this->getProposalType()
            );
        }

        if ($this->form->proposal) {
            app(ProposalService::class)->updateProposal(
                $this->form->proposal,
                $this->form,
                false // Disable global validation
            );
        } else {
            $proposal = app(ProposalService::class)->createProposal(
                $this->form,
                $this->getProposalType()
            );
            $this->form->proposal = $proposal;
        }

        // Force clear file input and reset iteration to clear frontend state
        $this->form->substance_file = null;
        $this->fileInputIteration++;

        $this->toastSuccess('Draft proposal berhasil disimpan.');
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
        return app(MasterDataService::class)->themes($this->form->focus_area_id ?: null);
    }

    #[Computed]
    public function topics()
    {
        return app(MasterDataService::class)->topics(
            $this->form->focus_area_id ?: null,
            $this->form->theme_id ?: null
        );
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
    public function clusterLevel1Options()
    {
        return $this->scienceClusters->whereNull('parent_id');
    }

    #[Computed]
    public function clusterLevel2Options()
    {
        return $this->scienceClusters->where('parent_id', $this->form->cluster_level1_id);
    }

    #[Computed]
    public function clusterLevel3Options()
    {
        return $this->scienceClusters->where('parent_id', $this->form->cluster_level2_id);
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
                'form.author_tasks' => 'required|string',
                'form.tkt_type' => $type === 'research' ? 'required|string|max:255' : 'nullable',
                'form.tkt_results' => $type === 'research' ? ['nullable', 'array', function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        return;
                    }

                    // 1. Calculate achieved level
                    $achievedLevel = 0;
                    // Get level models to map IDs to integer levels
                    $levels = \App\Models\TktLevel::whereIn('id', array_keys($value))->get();

                    foreach ($levels as $level) {
                        $data = $value[$level->id] ?? null;
                        // Check if passed (percentage >= 80)
                        if ($data && isset($data['percentage']) && $data['percentage'] >= 80) {
                            $achievedLevel = max($achievedLevel, $level->level);
                        }
                    }

                    // 2. Get required range for the scheme if selected
                    if ($this->form->research_scheme_id) {
                        $scheme = \App\Models\ResearchScheme::find($this->form->research_scheme_id);
                        if ($scheme && $scheme->strata) {
                            $range = \App\Livewire\Research\Proposal\Components\TktMeasurement::getTktRangeForStrata($scheme->strata);

                            // If range exists (not PKM), validate
                            if ($range) {
                                [$min, $max] = $range;

                                // Check if achieved level is within range
                                if ($achievedLevel < $min || $achievedLevel > $max) {
                                    $fail("TKT Saat Ini (Level $achievedLevel) tidak sesuai dengan Skema $scheme->strata (Target: Level $min - $max).");
                                }
                            }
                        }
                    }
                }] : 'nullable',
            ],
            2 => array_merge($this->getStep2Rules(), $type === 'research' ? [
                'form.outputs' => ['required', 'array', 'min:1', function ($attribute, $value, $fail) {
                    $wajibCount = collect($value)->where('category', 'Wajib')->count();
                    if ($wajibCount < 1) {
                        $fail('Minimal harus ada 1 luaran wajib untuk proposal penelitian.');
                    }

                    // Validate each row has required fields
                    foreach ($value as $index => $item) {
                        $rowNum = $index + 1;
                        $errors = [];

                        if (empty($item['group'])) {
                            $errors[] = 'Kategori Luaran';
                        }
                        if (empty($item['type'])) {
                            $errors[] = 'Luaran';
                        }
                        if (empty($item['status'])) {
                            $errors[] = 'Status';
                        }

                        if (! empty($errors)) {
                            $fail("Baris {$rowNum}: ".implode(', ', $errors).' wajib diisi.');
                        }
                    }
                }],
            ] : []),
            3 => [
                'form.budget_items' => ['required', 'array', 'min:1', function ($attribute, $value, $fail) {
                    foreach ($value as $index => $item) {
                        $rowNum = $index + 1;
                        $errors = [];

                        if (empty($item['budget_group_id'])) {
                            $errors[] = 'Kelompok RAB';
                        }
                        if (empty($item['budget_component_id'])) {
                            $errors[] = 'Komponen';
                        }
                        if (empty($item['item'])) {
                            $errors[] = 'Item';
                        }
                        if (empty($item['volume']) || (float) $item['volume'] <= 0) {
                            $errors[] = 'Volume';
                        }
                        if (empty($item['unit_price']) || (float) $item['unit_price'] <= 0) {
                            $errors[] = 'Harga Satuan';
                        }

                        if (! empty($errors)) {
                            $fail("Baris {$rowNum}: ".implode(', ', $errors).' wajib diisi.');
                        }
                    }
                }],
            ],
            4 => [
                'form.partner_ids' => 'nullable|array',
            ],
            default => [],
        };
    }
}
