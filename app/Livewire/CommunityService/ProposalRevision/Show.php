<?php

declare(strict_types=1);

namespace App\Livewire\CommunityService\ProposalRevision;

use App\Livewire\Forms\ProposalForm;
use App\Models\Partner;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Detail Revisi Proposal Pengabdian')]
class Show extends Component
{
    public ProposalForm $form;

    #[Validate('required|exists:partners,id')]
    public string $partnerId = '';

    #[Validate('required|string|min:50')]
    public string $partnerIssueSummary = '';

    #[Validate('required|string|min:50')]
    public string $solutionOffered = '';

    /**
     * Mount the component with proposal.
     */
    public function mount(Proposal $proposal): void
    {
        // if is research proposal, redirect to research show page
        if ($proposal->detailable instanceof \App\Models\Research) {
            $this->redirect(route('research.proposal-revision.show', $proposal->id), navigate: true);
        }

        // Eager load all required relationships for the show page
        $proposal->load([
            'submitter',
            'focusArea',
            // 'communityServiceScheme',
            'detailable.partner',
            'budgetItems.budgetGroup',
            'budgetItems.budgetComponent',
            'reviewers' => function ($q) {
                $q->where('status', 'completed')
                    ->with('user');
            },
        ]);

        $this->form->setProposal($proposal);

        // Initialize form values
        $communityService = $proposal->detailable;
        $this->partnerId = (string) ($communityService?->partner_id ?? '');
        $this->partnerIssueSummary = $communityService?->partner_issue_summary ?? '';
        $this->solutionOffered = $communityService?->solution_offered ?? '';
    }

    /**
     * Get all partners.
     */
    #[Computed]
    public function partners()
    {
        return Partner::orderBy('name')->get();
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
            $communityService = $this->form->proposal->detailable;

            // Update community service data
            $communityService->partner_id = $this->partnerId;
            $communityService->partner_issue_summary = $this->partnerIssueSummary;
            $communityService->solution_offered = $this->solutionOffered;

            $communityService->save();

            session()->flash('success', 'Perubahan berhasil disimpan');

            // Refresh proposal data
            $this->form->setProposal($this->form->proposal->fresh());
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan perubahan: '.$e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.community-service.proposal-revision.show', [
            'proposal' => $this->form->proposal,
        ]);
    }
}
