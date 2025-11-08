<?php

declare(strict_types=1);

namespace App\Livewire\Research\ProposalRevision;

use App\Livewire\Forms\ProposalForm;
use App\Models\MacroResearchGroup;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Detail Revisi Proposal Penelitian')]
class Show extends Component
{
    use WithFileUploads;

    public ProposalForm $form;

    #[Validate('required|exists:macro_research_groups,id')]
    public string $macroResearchGroupId = '';

    #[Validate('nullable|file|mimes:pdf,doc,docx|max:10240')]
    public $substanceFile = null;

    /**
     * Mount the component with proposal.
     */
    public function mount(Proposal $proposal): void
    {
        // if is community service proposal, redirect to community service show page
        if ($proposal->detailable instanceof \App\Models\CommunityService) {
            $this->redirect(route('community-service.proposal-revision.show', $proposal->id), navigate: true);
        }

        // Eager load all required relationships for the show page
        $proposal->load([
            'submitter',
            'focusArea',
            'researchScheme',
            'detailable.macroResearchGroup',
            'budgetItems.budgetGroup',
            'budgetItems.budgetComponent',
            'reviewers' => function ($q) {
                $q->where('status', 'completed')
                    ->with('user');
            },
        ]);

        $this->form->setProposal($proposal);

        // Initialize form values
        $this->macroResearchGroupId = (string) ($proposal->detailable?->macro_research_group_id ?? '');
    }

    /**
     * Get all macro research groups.
     */
    #[Computed]
    public function macroResearchGroups()
    {
        return MacroResearchGroup::orderBy('name')->get();
    }

    /**
     * Check if current user can edit the proposal.
     */
    public function canEdit(): bool
    {
        return $this->form->proposal->submitter_id === Auth::id();
    }

    /**
     * Save the revision changes.
     */
    public function save(): void
    {
        if (! $this->canEdit()) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengedit proposal ini');

            return;
        }

        $this->validate();

        try {
            $research = $this->form->proposal->detailable;

            // Update macro research group
            $research->macro_research_group_id = $this->macroResearchGroupId;

            // Handle file upload
            if ($this->substanceFile) {
                // Delete old file if exists
                if ($research->substance_file) {
                    Storage::delete($research->substance_file);
                }

                // Store new file
                $path = $this->substanceFile->store('proposals/substance-files', 'public');
                $research->substance_file = $path;
            }

            $research->save();

            session()->flash('success', 'Perubahan berhasil disimpan');

            // Refresh proposal data
            $this->form->setProposal($this->form->proposal->fresh());
            $this->macroResearchGroupId = (string) ($research->macro_research_group_id ?? '');

            // Reset file input
            $this->substanceFile = null;
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan perubahan: '.$e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.research.proposal-revision.show', [
            'proposal' => $this->form->proposal,
        ]);
    }
}
