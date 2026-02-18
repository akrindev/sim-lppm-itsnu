<?php

declare(strict_types=1);

namespace App\Livewire\CommunityService\ProposalRevision;

use App\Livewire\Concerns\HasToast;
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
    use HasToast;

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
        // Redirect if wrong type
        if ($proposal->detailable_type !== \App\Models\CommunityService::class) {
            if (str_contains($proposal->detailable_type, 'Research')) {
                $this->redirect(route('research.proposal-revision.show', $proposal->id), navigate: true);
            } else {
                abort(404);
            }

            return;
        }

        // Eager load all required relationships for the show page
        $proposal->load([
            'submitter.identity',
            'focusArea',
            'researchScheme',
            'detailable.partner',
            'budgetItems.budgetGroup',
            'budgetItems.budgetComponent',
            'reviewers' => function ($q) {
                $q->where('status', 'completed')
                    ->with(['user', 'scores.criteria']);
            },
            'reviewLogs.user',
            'reviewLogs.scores.criteria',
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
        $user = Auth::user();

        // Admin LPPM can edit any proposal that is not completed
        if ($user->hasRole(['admin lppm']) && $this->form->proposal->status !== \App\Enums\ProposalStatus::COMPLETED) {
            return true;
        }

        // Regular users (dosen) can only edit their own proposals
        return $this->form->proposal->submitter_id === $user->id;
    }

    /**
     * Save the revision changes.
     */
    public function save(): void
    {
        if (! $this->canEdit()) {
            $message = 'Anda tidak memiliki akses untuk mengedit proposal ini';
            session()->flash('error', $message);
            $this->toastError($message);

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

            $message = 'Perubahan berhasil disimpan';
            session()->flash('success', $message);
            $this->toastSuccess($message);

            // Refresh proposal data
            $this->form->setProposal($this->form->proposal->fresh());
        } catch (\Exception $e) {
            $message = 'Gagal menyimpan perubahan: '.$e->getMessage();
            session()->flash('error', $message);
            $this->toastError($message);
        }
    }

    public function render(): View
    {
        return view('livewire.community-service.proposal-revision.show', [
            'proposal' => $this->form->proposal,
        ]);
    }
}
