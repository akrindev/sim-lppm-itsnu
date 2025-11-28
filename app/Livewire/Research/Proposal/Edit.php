<?php

namespace App\Livewire\Research\Proposal;

use App\Livewire\Forms\ProposalForm;
use App\Models\BudgetComponent;
use App\Models\BudgetGroup;
use App\Models\FocusArea;
use App\Models\MacroResearchGroup;
use App\Models\NationalPriority;
use App\Models\Partner;
use App\Models\Proposal;
use App\Models\ResearchScheme;
use App\Models\ScienceCluster;
use App\Models\Theme;
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
#[Title('Edit Proposal Penelitian')]
class Edit extends Component
{
    use WithFileUploads;

    public ProposalForm $form;

    public int $currentStep = 1;

    public string $author_name = '';

    public string $componentId;

    /**
     * Mount the component with proposal data.
     */
    public function mount(Proposal $proposal): void
    {
        // Check authorization - user can only edit their own proposals
        if ($proposal->submitter_id !== Auth::user()->id) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengedit proposal ini');
            $this->redirect(route('research.proposal.index'));

            return;
        }

        // Set author name
        $this->author_name = Str::title(Auth::user()->name . ' (' . Auth::user()->identity->identity_id . ')');

        // Generate a unique, stable component ID for this instance
        $this->componentId = 'lwc-' . Str::random(10);

        // Load proposal data into form
        $this->form->setProposal($proposal);
    }

    /**
     * Handle members updated event from TeamMembersForm
     */
    #[On('members-updated')]
    public function updateMembers(array $members): void
    {
        $this->form->members = $members;
    }

    /**
     * Update the proposal using the form object
     */
    public function save(): void
    {
        $this->form->validate();

        try {
            $this->form->update();

            session()->flash('success', 'Proposal penelitian berhasil diperbarui');

            $this->redirect(route('research.proposal.show', $this->form->proposal), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui proposal: ' . $e->getMessage());
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
                'form.substance_file' => 'nullable|file|mimes:pdf|max:10240',
                'form.outputs' => ['required', 'array', 'min:1', function ($attribute, $value, $fail) {
                    $hasWajib = collect($value)->contains('category', 'Wajib');
                    if (!$hasWajib) {
                        $fail('Harus ada setidaknya satu luaran dengan kategori Wajib.');
                    }
                }],
            ]),
            3 => $this->validate([
                'form.budget_items' => 'required|array|min:1',
            ]),
            4 => $this->validate([
                'form.partner_ids' => 'nullable|array',
            ]),
            default => null,
        };
    }

    public function addOutput(): void
    {
        $this->form->outputs[] = [
            'year' => 1,
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
            }
        }
    }

    public function saveNewPartner(): void
    {
        $this->validate([
            'form.new_partner.name' => 'required|string|max:255',
            'form.new_partner.email' => 'nullable|email',
            'form.new_partner.institution' => 'nullable|string|max:255',
            'form.new_partner.country' => 'nullable|string|max:255',
            'form.new_partner.address' => 'nullable|string',
            'form.new_partner_commitment_file' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $partner = Partner::create([
            'name' => $this->form->new_partner['name'],
            'email' => $this->form->new_partner['email'],
            'institution' => $this->form->new_partner['institution'],
            'country' => $this->form->new_partner['country'],
            'address' => $this->form->new_partner['address'],
        ]);

        // Upload commitment letter using Media Library
        if ($this->form->new_partner_commitment_file) {
            $partner
                ->addMedia($this->form->new_partner_commitment_file->getRealPath())
                ->usingName($this->form->new_partner_commitment_file->getClientOriginalName())
                ->usingFileName($this->form->new_partner_commitment_file->hashName())
                ->withCustomProperties(['uploaded_by' => auth()->id()])
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
        session()->flash('success', 'Mitra berhasil ditambahkan');
    }

    public function render(): View
    {
        return view('livewire.research.proposal.edit');
    }
}
