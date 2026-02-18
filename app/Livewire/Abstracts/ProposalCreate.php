<?php

namespace App\Livewire\Abstracts;

use App\Constants\ProposalConstants;
use App\Livewire\Concerns\HasToast;
use App\Livewire\Forms\ProposalForm;
use App\Livewire\Traits\WithProposalWizard;
use App\Livewire\Traits\WithStepWizard;
use App\Models\BudgetCap;
use App\Services\BudgetValidationService;
use App\Services\MasterDataService;
use App\Services\ProposalService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
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
        $this->author_name = Auth::user()?->name ?? '';

        // Handle route model binding (if passed as object) or ID string
        $proposalToLoad = $proposal ?? ($proposalId ? \App\Models\Proposal::find($proposalId) : null);

        if ($proposalToLoad) {
            if (! $this->canEditProposal($proposalToLoad)) {
                abort(403);
            }

            $this->form->setProposal($proposalToLoad);
        } else {
            // Initial values for new proposals
            $this->form->start_year = date('Y');

            // Add initial empty budget row
            if (empty($this->form->budget_items)) {
                $this->addBudgetItem();
            }
        }
    }

    protected function canEditProposal(\App\Models\Proposal $proposal): bool
    {
        $user = Auth::user();

        if ($proposal->status === \App\Enums\ProposalStatus::COMPLETED) {
            return false;
        }

        if ($user->hasRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita'])) {
            return true;
        }

        return $proposal->status === \App\Enums\ProposalStatus::DRAFT
            && $proposal->submitter_id === $user->id;
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
            'form.outputs.*.year' => 'Tahun Ke-',
            'form.outputs.*.category' => 'Jenis',
            'form.outputs.*.group' => 'Kategori Luaran',
            'form.outputs.*.type' => 'Luaran',
            'form.outputs.*.status' => 'Status',
            'form.outputs.*.description' => 'Keterangan (URL)',
            'form.budget_items' => 'RAB',
            'form.budget_items.*.year' => 'Tahun Ke-',
            'form.budget_items.*.budget_group_id' => 'Kelompok Anggaran',
            'form.budget_items.*.budget_component_id' => 'Komponen Anggaran',
            'form.budget_items.*.item' => 'Nama Item',
            'form.budget_items.*.volume' => 'Volume',
            'form.budget_items.*.unit_price' => 'Nominal Satuan',
            'form.partner_ids' => 'Mitra',
            'form.new_partner.name' => 'Nama Mitra',
            'form.new_partner.email' => 'Email Mitra',
            'form.new_partner.institution' => 'Institusi Mitra',
            'form.new_partner.country' => 'Negara Mitra',
            'form.new_partner.type' => 'Jenis Mitra',
            'form.new_partner.address' => 'Alamat Mitra',
            'form.new_partner_commitment_file' => 'Surat Kesanggupan Mitra',
        ];
    }

    public function messages(): array
    {
        return [
            'form.outputs.*.group.required' => 'Baris :position: Kategori Luaran wajib diisi.',
            'form.outputs.*.type.required' => 'Baris :position: Luaran wajib diisi.',
            'form.outputs.*.status.required' => 'Baris :position: Status wajib diisi.',
            'form.outputs.*.status.in' => 'Baris :position: Status tidak valid. Pilih salah satu: '.implode(', ', ProposalConstants::OUTPUT_STATUSES).'.',
            'form.outputs.*.description.required' => 'Baris :position: Keterangan (URL) wajib diisi.',
            'form.budget_items.*.budget_group_id.required' => 'Baris :position: Kelompok Anggaran wajib diisi.',
            'form.budget_items.*.budget_component_id.required' => 'Baris :position: Komponen Anggaran wajib diisi.',
            'form.budget_items.*.item.required' => 'Baris :position: Nama Item wajib diisi.',
            'form.budget_items.*.unit_price.required' => 'Baris :position: Nominal Satuan wajib diisi.',
            'form.budget_items.*.unit_price.min' => 'Baris :position: Nominal Satuan minimal Rp1.',
            'form.budget_items.*.volume.required' => 'Baris :position: Volume wajib diisi.',
            'form.budget_items.*.volume.min' => 'Baris :position: Volume minimal 0.01.',
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
        try {
            $this->form->validate();

            $validationYear = (int) ($this->form->start_year ?: date('Y'));

            app(BudgetValidationService::class)->validateBudgetGroupPercentages(
                $this->form->budget_items,
                $this->getProposalType(),
                $validationYear
            );

            app(BudgetValidationService::class)->validateBudgetCap(
                $this->form->budget_items,
                $this->getProposalType(),
                $validationYear
            );

            if ($this->form->proposal) {
                app(ProposalService::class)->updateProposal(
                    $this->form->proposal,
                    $this->form
                );
                $proposal = $this->form->proposal;
                $message = 'Proposal berhasil diperbarui.';
            } else {
                $proposal = app(ProposalService::class)->createProposal(
                    $this->form,
                    $this->getProposalType()
                );
                $message = 'Proposal berhasil dibuat.';
            }

            session()->flash('success', $message);
            $this->toastSuccess($message);

            $this->redirect($this->getShowRoute($proposal->id));
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->errors());
            $this->toastValidationError($exception);
        }
    }

    public function nextStep(): void
    {
        try {
            $this->validateCurrentStep();

            if ($this->currentStep < 5) {
                $this->currentStep++;
            }
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->errors());
            $this->toastValidationError($exception);
        }
    }

    public function saveDraft(): void
    {
        try {
            $rules = $this->getStepValidationRules($this->currentStep);
            if (! empty($rules)) {
                $this->validate($rules);
            }

            if ($this->currentStep === 3 && ! empty($this->form->budget_items)) {
                $validationYear = (int) ($this->form->start_year ?: date('Y'));

                app(BudgetValidationService::class)->validateBudgetGroupPercentages(
                    $this->form->budget_items,
                    $this->getProposalType(),
                    $validationYear
                );

                app(BudgetValidationService::class)->validateBudgetCap(
                    $this->form->budget_items,
                    $this->getProposalType(),
                    $validationYear
                );
            }

            if ($this->form->proposal) {
                app(ProposalService::class)->updateProposal(
                    $this->form->proposal,
                    $this->form,
                    false
                );
            } else {
                $proposal = app(ProposalService::class)->createProposal(
                    $this->form,
                    $this->getProposalType()
                );
                $this->form->proposal = $proposal;
            }

            $this->form->substance_file = null;
            $this->fileInputIteration++;

            $message = 'Draft proposal berhasil disimpan.';
            session()->flash('success', $message);
            $this->toastSuccess($message);
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->errors());
            $this->toastValidationError($exception);
        }
    }

    private function toastValidationError(ValidationException $exception): void
    {
        $firstError = collect($exception->errors())->flatten()->first();
        $this->toastError($firstError ?: 'Terdapat kesalahan pada input form.');
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

    #[Computed]
    public function approvalTemplateUrl(): ?string
    {
        return app(MasterDataService::class)->getApprovalTemplateUrl($this->getProposalType());
    }

    #[Computed]
    public function partnerCommitmentTemplateUrl(): ?string
    {
        return app(MasterDataService::class)->getPartnerCommitmentTemplateUrl($this->getProposalType());
    }

    #[Computed]
    public function budgetCapMissingMessage(): ?string
    {
        $year = (int) ($this->form->start_year ?: date('Y'));
        $proposalType = $this->getProposalType();
        $budgetCap = BudgetCap::getCapForYear($year, $proposalType);

        if ($budgetCap !== null && $budgetCap > 0) {
            return null;
        }

        $proposalLabel = $proposalType === 'research' ? 'Penelitian' : 'PKM';

        return "Batas anggaran RAB untuk {$proposalLabel} tahun {$year} belum diatur. Silakan hubungi Admin LPPM sebelum melanjutkan.";
    }

    protected function getStepValidationRules(int $step): array
    {
        $type = $this->getProposalType();

        return match ($step) {
            1 => array_merge([
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
            ], $type === 'research' ? [
                'form.tkt_type' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::in(app(\App\Services\MasterDataService::class)->tktTypes()->toArray())],
                'form.tkt_results' => ['nullable', 'array', function ($attribute, $value, $fail) {
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
                }],
            ] : []),
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
                        if (empty($item['description'])) {
                            $errors[] = 'Keterangan (URL)';
                        }

                        if (! empty($errors)) {
                            $fail("Baris {$rowNum}: ".implode(', ', $errors).' wajib diisi.');
                        }
                    }
                }],
            ] : []),
            3 => [
                'form.budget_items' => ['required', 'array', 'min:1', function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        return;
                    }

                    $validationYear = (int) ($this->form->start_year ?: date('Y'));

                    try {
                        app(BudgetValidationService::class)->validateBudgetGroupPercentages(
                            $value,
                            $this->getProposalType(),
                            $validationYear
                        );

                        app(BudgetValidationService::class)->validateBudgetCap(
                            $value,
                            $this->getProposalType(),
                            $validationYear
                        );
                    } catch (\Illuminate\Validation\ValidationException $exception) {
                        foreach (($exception->errors()['budget_items'] ?? []) as $errorMessage) {
                            $fail($errorMessage);
                        }
                    }
                }],
                'form.budget_items.*.year' => 'required|integer|min:1|max:10',
                'form.budget_items.*.budget_group_id' => 'required|exists:budget_groups,id',
                'form.budget_items.*.budget_component_id' => 'required|exists:budget_components,id',
                'form.budget_items.*.item' => 'required|string|max:255',
                'form.budget_items.*.volume' => 'required|numeric|min:0.01',
                'form.budget_items.*.unit_price' => 'required|numeric|min:1',
            ],
            4 => [
                'form.partner_ids' => 'nullable|array',
                'form.partner_ids.*' => 'exists:partners,id',
            ],
            default => [],
        };
    }

    public function render()
    {
        // dump("Rendering ProposalCreate, Auth check: " . (Auth::check() ? 'Yes' : 'No'));
        return view($this->getProposalType() === 'research' ? 'livewire.research.proposal.create' : 'livewire.community-service.proposal.create');
    }
}
