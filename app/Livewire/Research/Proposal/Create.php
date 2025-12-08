<?php

namespace App\Livewire\Research\Proposal;

use App\Livewire\Forms\ProposalForm;
use App\Models\BudgetComponent;
use App\Models\BudgetGroup;
use App\Models\FocusArea;
use App\Models\MacroResearchGroup;
use App\Models\NationalPriority;
use App\Models\Partner;
use App\Models\ResearchScheme;
use App\Models\ScienceCluster;
use App\Models\Theme;
use App\Models\TktLevel;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Buat Proposal Penelitian')]
class Create extends Component
{
    use WithFileUploads;

    public ProposalForm $form;

    public int $currentStep = 1;

    public string $author_name = '';

    // Real-time budget validation errors
    public array $budgetValidationErrors = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->author_name = Str::title(Auth::user()->name . ' (' . Auth::user()->identity->identity_id . ')');

        $startDate = \App\Models\Setting::where('key', 'research_proposal_start_date')->value('value');
        $endDate = \App\Models\Setting::where('key', 'research_proposal_end_date')->value('value');

        if ($startDate && $endDate) {
            $now = now();
            $start = \Carbon\Carbon::parse($startDate)->startOfDay();
            $end = \Carbon\Carbon::parse($endDate)->endOfDay();

            if (! $now->between($start, $end)) {
                session()->flash('error', 'Masa pengajuan proposal penelitian telah berakhir atau belum dimulai.');
                $this->redirect(route('research.proposal.index'));
            }
        }
    }

    /**
     * Handle members updated event from TeamMembersForm
     */
    #[On('members-updated')]
    public function updateMembers(array $members): void
    {
        $this->form->members = $members;
    }

    #[On('tkt-calculated')]
    public function updateTktResults(array $levelResults, array $indicatorScores): void
    {
        $this->form->tkt_results = $levelResults;
        $this->form->tkt_indicator_scores = $indicatorScores;
    }

    /**
     * Save the proposal using the form object
     */
    public function save(): void
    {
        $this->form->validate();

        try {
            $proposal = $this->form->store(Auth::user()->getKey());
            session()->flash('success', 'Proposal penelitian berhasil dibuat');
            $this->redirect(route('research.proposal.show', $proposal));
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat proposal: ' . $e->getMessage());
        }
    }

    #[Computed]
    public function schemes()
    {
        return ResearchScheme::all();
    }

    #[Computed]
    public function focusAreas()
    {
        return FocusArea::all();
    }

    #[Computed]
    public function themes()
    {
        return Theme::all();
    }

    #[Computed]
    public function topics()
    {
        return Topic::all();
    }

    #[Computed]
    public function nationalPriorities()
    {
        return NationalPriority::all();
    }

    #[Computed]
    public function scienceClusters()
    {
        return ScienceCluster::all();
    }

    #[Computed]
    public function macroResearchGroups()
    {
        return MacroResearchGroup::all();
    }

    #[Computed]
    public function partners()
    {
        return Partner::all();
    }

    #[Computed]
    public function budgetGroups()
    {
        return BudgetGroup::all();
    }

    #[Computed]
    public function budgetComponents()
    {
        return BudgetComponent::with('budgetGroup')->get();
    }

    #[Computed]
    public function tktTypes()
    {
        return TktLevel::select('type')->distinct()->pluck('type');
    }

    #[Computed]
    public function templateUrl()
    {
        $setting = \App\Models\Setting::where('key', 'research_proposal_template')->first();
        if ($setting && $setting->hasMedia('template')) {
            return $setting->getFirstMedia('template')->getUrl();
        }

        return null;
    }

    public function nextStep(): void
    {
        $this->validateCurrentStep();

        if ($this->currentStep < 5) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    private function validateCurrentStep(): void
    {
        match ($this->currentStep) {
            1 => $this->validate([
                'form.title' => 'required|string|max:255',
                'form.research_scheme_id' => 'required|exists:research_schemes,id',
                'form.duration_in_years' => 'required|integer|min:1|max:10',
                'form.focus_area_id' => 'required|exists:focus_areas,id',
                'form.theme_id' => 'required|exists:themes,id',
                'form.topic_id' => 'required|exists:topics,id',
                'form.cluster_level1_id' => 'required|exists:science_clusters,id',
                'form.summary' => 'required|string|min:100',
                'form.author_tasks' => 'required',
                'form.members' => 'required|array|min:1',
            ]),
            2 => $this->validate([
                'form.macro_research_group_id' => 'required|exists:macro_research_groups,id',
                'form.substance_file' => 'required|file|mimes:pdf|max:10240',
                'form.outputs' => ['required', 'array', 'min:1', function ($attribute, $value, $fail) {
                    $hasWajib = collect($value)->contains('category', 'Wajib');
                    if (! $hasWajib) {
                        $fail('Harus ada setidaknya satu luaran dengan kategori Wajib.');
                    }
                }],
            ]),
            3 => $this->validate([
                'form.budget_items' => ['required', 'array', 'min:1', function ($attribute, $value, $fail) {
                    // Validate budget group percentages
                    try {
                        $this->form->validateBudgetGroupPercentages();
                    } catch (\Illuminate\Validation\ValidationException $e) {
                        $errors = $e->errors()['budget_items'] ?? [];
                        foreach ($errors as $error) {
                            $fail($error);
                        }
                    }

                    // Validate year-based budget cap
                    try {
                        $this->form->validateBudgetCap('research');
                    } catch (\Illuminate\Validation\ValidationException $e) {
                        $errors = $e->errors()['budget_items'] ?? [];
                        foreach ($errors as $error) {
                            $fail($error);
                        }
                    }
                }],
            ]),
            4 => $this->validate([
                'form.partner_ids' => 'nullable|array',
                // Validate that all selected partners have complete data
                'form.partner_ids.*' => [
                    'required',
                    'exists:partners,id',
                    function ($attribute, $value, $fail) {
                        $partner = Partner::find($value);
                        if ($partner && (empty($partner->institution) || empty($partner->country))) {
                            $fail("Mita '{$partner->name}' harus memiliki data lengkap (Institusi dan Negara wajib diisi)");
                        }
                    },
                ],
            ]),
            default => null,
        };

        // Clear budget validation errors after successful validation
        if ($this->currentStep === 3) {
            $this->budgetValidationErrors = [];
        }
    }

    public function addOutput(): void
    {
        $this->form->outputs[] = [
            'year' => 1, // date('Y'),
            'category' => 'Wajib',
            'group' => '',
            'type' => '',
            'status' => '',
            'description' => '',
        ];
    }

    public function removeOutput(int $index): void
    {
        unset($this->form->outputs[$index]);
        $this->form->outputs = array_values($this->form->outputs);
    }

    public function addBudgetItem(): void
    {
        $this->form->budget_items[] = [
            'budget_group_id' => '',
            'budget_component_id' => '',
            'group' => '',
            'component' => '',
            'item' => '',
            'unit' => '',
            'volume' => '',
            'unit_price' => '',
            'total' => '',
        ];
    }

    public function removeBudgetItem(int $index): void
    {
        unset($this->form->budget_items[$index]);
        $this->form->budget_items = array_values($this->form->budget_items);
    }

    public function calculateTotal(int $index): void
    {
        $item = &$this->form->budget_items[$index];
        $volume = floatval($item['volume'] ?? 0);
        $unitPrice = floatval($item['unit_price'] ?? 0);
        $item['total'] = $volume * $unitPrice;

        // Validate budget in real-time
        $this->validateBudgetRealtime();
    }

    public function updated($property, $value): void
    {
        // Handle budget items updates
        if (Str::startsWith($property, 'form.budget_items.')) {
            $parts = explode('.', $property);
            // form.budget_items.0.budget_component_id
            if (count($parts) === 4) {
                $index = (int) $parts[2];
                $field = $parts[3];

                // Auto-fill unit when budget_component_id is selected
                if ($field === 'budget_component_id' && ! empty($this->form->budget_items[$index]['budget_component_id'])) {
                    $componentId = $this->form->budget_items[$index]['budget_component_id'];
                    $component = BudgetComponent::find($componentId);

                    if ($component) {
                        $this->form->budget_items[$index]['unit'] = $component->unit;
                    }
                }

                // Validate budget in real-time whenever budget items change
                if (in_array($field, ['budget_group_id', 'volume', 'unit_price', 'total'])) {
                    $this->validateBudgetRealtime();
                }
            }
        }
    }

    public function saveNewPartner(): void
    {
        $this->validate([
            'form.new_partner.name' => 'required|string|max:255',
            'form.new_partner.email' => 'nullable|email',
            'form.new_partner.institution' => 'required|string|max:255',
            'form.new_partner.country' => 'required|string|max:255',
            'form.new_partner.address' => 'nullable|string',
            'form.new_partner_commitment_file' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $partner = Partner::create([
            'name' => $this->form->new_partner['name'],
            'email' => $this->form->new_partner['email'],
            'institution' => $this->form->new_partner['institution'],
            'country' => $this->form->new_partner['country'],
            'address' => $this->form->new_partner['address'],
            'type' => 'External', // Default value for partner type
        ]);

        // Upload commitment letter using Media Library
        if ($this->form->new_partner_commitment_file) {
            $partner
                ->addMedia($this->form->new_partner_commitment_file->getRealPath())
                ->usingName($this->form->new_partner_commitment_file->getClientOriginalName())
                ->usingFileName($this->form->new_partner_commitment_file->hashName())
                ->withCustomProperties(['uploaded_by' => Auth::id()])
                ->toMediaCollection('commitment_letter');
        }

        $this->form->partner_ids[] = $partner->id;

        // Reset form
        $this->form->new_partner = [
            'name' => '',
            'email' => '',
            'institution' => '',
            'country' => '',
            'address' => '',
        ];
        $this->form->new_partner_commitment_file = null;

        $this->dispatch('partner-added');
        $this->dispatch('close-modal', detail: ['modalId' => 'modal-partner']);
        session()->flash('success', 'Mitra berhasil ditambahkan');
    }

    public function render(): View
    {
        return view('livewire.research.proposal.create');
    }

    /**
     * Validate budget in real-time and display errors without blocking.
     * This provides immediate feedback to users as they add/edit budget items.
     * Percentages are calculated based on the budget cap, not the total entered.
     */
    private function validateBudgetRealtime(): void
    {
        $this->budgetValidationErrors = [];

        if (empty($this->form->budget_items)) {
            return;
        }

        // Get budget cap for current year
        $currentYear = (int) date('Y');
        $budgetCap = \App\Models\BudgetCap::getCapForYear($currentYear, 'research');

        if ($budgetCap === null || $budgetCap <= 0) {
            $this->budgetValidationErrors[] = sprintf(
                'Batas anggaran untuk Penelitian tahun %s belum diatur.',
                $currentYear
            );

            return;
        }

        // Check budget group percentages (based on budget cap)
        $budgetGroups = BudgetGroup::whereNotNull('percentage')->get();

        foreach ($budgetGroups as $group) {
            $groupTotal = collect($this->form->budget_items)
                ->where('budget_group_id', $group->id)
                ->sum(fn($item) => (float) ($item['total'] ?? 0));

            // Calculate percentage based on BUDGET CAP, not total entered
            $percentageUsed = ($groupTotal / $budgetCap) * 100;
            $allowedPercentage = (float) $group->percentage;

            if ($percentageUsed > $allowedPercentage) {
                $this->budgetValidationErrors[] = sprintf(
                    '%s melebihi batas maksimal %s%%. Saat ini: %s%% (Rp %s dari batas anggaran Rp %s)',
                    $group->name,
                    number_format($allowedPercentage, 0),
                    number_format($percentageUsed, 2),
                    number_format($groupTotal, 0, ',', '.'),
                    number_format($budgetCap, 0, ',', '.')
                );
            }
        }

        // Check year-based budget cap (total cannot exceed cap)
        $totalBudget = collect($this->form->budget_items)->sum(fn($item) => (float) ($item['total'] ?? 0));

        if ($totalBudget > $budgetCap) {
            $this->budgetValidationErrors[] = sprintf(
                'Total anggaran melebihi batas maksimal untuk Penelitian tahun %s. Batas: Rp %s, Total saat ini: Rp %s',
                $currentYear,
                number_format($budgetCap, 0, ',', '.'),
                number_format($totalBudget, 0, ',', '.')
            );
        }
    }
}
