<?php

namespace App\Livewire\Abstracts;

use App\Enums\ProposalStatus;
use App\Livewire\Forms\ProposalForm;
use App\Livewire\Traits\WithApproval;
use App\Livewire\Traits\WithTeamManagement;
use App\Models\Proposal;
use App\Services\ProposalService;
use Livewire\Attributes\Computed;
use Livewire\Component;

abstract class ProposalShow extends Component
{
    use WithApproval;
    use WithTeamManagement;

    public ProposalForm $form;

    public Proposal $proposal;

    protected ProposalService $proposalService;

    public function mount(Proposal $proposal): void
    {
        $this->proposal = $proposal;
        $this->proposalService = app(ProposalService::class);

        $this->form->setProposal($proposal);
    }

    abstract protected function getProposalType(): string;

    abstract protected function getIndexRoute(): string;

    abstract protected function getEditRoute(string $proposalId): string;

    abstract protected function getReviewRoute(string $proposalId): string;

    protected function getProposal(): Proposal
    {
        return $this->proposal;
    }

    public function delete(): void
    {
        if (! $this->canDelete) {
            abort(403, 'Hanya proposal dengan status draft yang dapat dihapus.');
        }

        $this->proposalService->deleteProposal($this->proposal);

        $this->redirectRoute($this->getIndexRoute());
    }

    public function edit(): void
    {
        $this->redirectRoute($this->getEditRoute($this->proposal->id));
    }

    public function review(): void
    {
        $this->redirectRoute($this->getReviewRoute($this->proposal->id));
    }

    #[Computed]
    public function statusLabel(): string
    {
        return $this->proposal->status?->label() ?? '';
    }

    #[Computed]
    public function statusColor(): string
    {
        return $this->proposal->status?->color() ?? 'secondary';
    }

    #[Computed]
    public function canEdit(): bool
    {
        return $this->proposal->status === ProposalStatus::DRAFT;
    }

    #[Computed]
    public function canDelete(): bool
    {
        return $this->proposal->status === ProposalStatus::DRAFT;
    }

    public function render()
    {
        return view($this->getViewName());
    }

    abstract protected function getViewName(): string;
}
