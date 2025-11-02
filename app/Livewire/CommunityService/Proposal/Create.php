<?php

namespace App\Livewire\CommunityService\Proposal;

use App\Livewire\Forms\ProposalForm;
use App\Models\BudgetComponent;
use App\Models\BudgetGroup;
use App\Models\FocusArea;
use App\Models\MacroResearchGroup;
use App\Models\NationalPriority;
use App\Models\Partner;
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
#[Title('Buat Proposal Pengabdian Masyarakat')]
class Create extends Component
{
    use WithFileUploads;

    public ProposalForm $form;

    public int $currentStep = 1;

    public string $author_name = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->author_name = Str::title(Auth::user()->name . ' (' . Auth::user()->identity->identity_id . ')');
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
     * Save the proposal using the form object (Community Service type)
     */
    public function save(): void
    {
        // Validate the form before attempting to store
        try {
            $this->form->validate();
        } catch (\Exception $e) {
            dd($e->getMessage());
            // Validation failed, errors will be displayed in the form
            // Do NOT reset the form - keep the data so user can correct errors
            return;
        }

        try {
            $proposal = $this->form->storeCommunityService(Auth::user()->getKey());
            session()->flash('success', 'Proposal pengabdian masyarakat berhasil dibuat');
            $this->redirect(route('community-service.proposal.show', $proposal));
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat proposal: ' . $e->getMessage());
        }
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
    public function partners()
    {
        return Partner::all();
    }

    #[Computed]
    public function macroResearchGroups()
    {
        return MacroResearchGroup::all();
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
                'form.duration_in_years' => 'required|integer|min:1|max:10',
                'form.focus_area_id' => 'required|exists:focus_areas,id',
                'form.theme_id' => 'required|exists:themes,id',
                'form.topic_id' => 'required|exists:topics,id',
                'form.cluster_level1_id' => 'required|exists:science_clusters,id',
                'form.summary' => 'required|string|min:100',
                'form.author_tasks' => 'required',
            ]),
            2 => $this->validate([
                'form.macro_research_group_id' => 'nullable|exists:macro_research_groups,id',
                'form.substance_file' => 'nullable|file|mimes:pdf|max:10240',
            ]),
            3 => $this->validate([
                'form.budget_items' => 'nullable|array',
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
            'year' => date('Y'),
            'category' => '',
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
            'commitment_letter_file' => $this->form->new_partner_commitment_file
                ? $this->form->new_partner_commitment_file->store('partner-commitments', 'public')
                : null,
        ]);

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
        return view('livewire.community-service.proposal.create');
    }
}
