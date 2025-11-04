<?php

namespace App\Livewire\Research\Proposal;

use App\Models\Proposal;
use App\Models\Research;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = 'all';

    #[Url]
    public string $yearFilter = '';

    public string $confirmingDeleteProposalId = '';

    public function resetFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->yearFilter = '';
        $this->resetPage();
    }

    public function confirmDeleteProposal(string $id): void
    {
        $this->confirmingDeleteProposalId = $id;
    }

    public function cancelDeleteProposal(): void
    {
        $this->confirmingDeleteProposalId = '';
    }

    public function render(): View
    {
        return view('livewire.research.proposal.index');
    }

    #[Computed]
    public function proposals()
    {
        $query = Proposal::query()
            ->where('detailable_type', Research::class);

        // Show all proposals for admin, kepala lppm, and rektor roles
        $user = Auth::user();
        $isAdmin = $user->activeHasAnyRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor']);

        if (! $isAdmin) {
            // Regular users see their own proposals OR proposals where they are team members
            $query->where(function ($q) use ($user) {
                $q->where('submitter_id', $user->id)
                    ->orWhereHas('teamMembers', function ($teamQuery) use ($user) {
                        $teamQuery->where('user_id', $user->id);
                    });
            });
        }

        return $query
            ->with(['submitter', 'focusArea', 'researchScheme'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('summary', 'like', "%{$this->search}%");
                });
            })
            ->when($isAdmin, function ($query) {
                // where status is not draft for admin users
                $query->where('status', '!=', 'draft');
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->yearFilter, function ($query) {
                $query->whereYear('created_at', $this->yearFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    #[Computed]
    public function statusStats(): array
    {
        $stats = [
            'all' => 0,
            'draft' => 0,
            'submitted' => 0,
            'reviewed' => 0,
            'approved' => 0,
            'rejected' => 0,
            'completed' => 0,
        ];

        $user = Auth::user();
        $isAdmin = $user->activeHasAnyRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor']);

        $query = Proposal::where('detailable_type', Research::class);
        if (! $isAdmin) {
            $query->where(function ($q) use ($user) {
                $q->where('submitter_id', $user->id)
                    ->orWhereHas('teamMembers', function ($teamQuery) use ($user) {
                        $teamQuery->where('user_id', $user->id);
                    });
            });
        }

        $query->get(['status'])
            ->each(function ($proposal) use (&$stats) {
                $stats['all']++;
                if (isset($stats[$proposal->status->value])) {
                    $stats[$proposal->status->value]++;
                }
            });

        return $stats;
    }

    #[Computed]
    public function typeStats(): array
    {
        $user = Auth::user();
        $isAdmin = $user->activeHasAnyRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor']);

        $query = Proposal::where('detailable_type', Research::class);
        if (! $isAdmin) {
            $query->where(function ($q) use ($user) {
                $q->where('submitter_id', $user->id)
                    ->orWhereHas('teamMembers', function ($teamQuery) use ($user) {
                        $teamQuery->where('user_id', $user->id);
                    });
            });
        }

        return [
            'all' => (clone $query)->count(),
            'active' => (clone $query)->whereIn('status', ['submitted', 'under_review', 'approved'])->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
        ];
    }

    #[Computed]
    public function availableYears(): array
    {
        $user = Auth::user();
        $isAdmin = $user->activeHasAnyRole(['admin lppm', 'admin lppm saintek', 'admin lppm dekabita', 'kepala lppm', 'rektor']);

        $query = Proposal::where('detailable_type', Research::class);
        if (! $isAdmin) {
            $query->where(function ($q) use ($user) {
                $q->where('submitter_id', $user->id)
                    ->orWhereHas('teamMembers', function ($teamQuery) use ($user) {
                        $teamQuery->where('user_id', $user->id);
                    });
            });
        }

        $years = $query
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        return $years;
    }

    public function deleteProposal(string $id): void
    {
        $proposal = Proposal::find($id);

        if (! $proposal) {
            session()->flash('error', 'Proposal tidak ditemukan');

            return;
        }

        // if proposal is completed, it cannot be deleted
        if ($proposal->status === 'completed') {
            session()->flash('error', 'Proposal yang sudah selesai tidak dapat dihapus');

            return;
        }

        $user = Auth::user();
        // Only admin lppm and submitter with status is not completed can delete proposals
        if (! $user->hasRole('admin lppm') && $proposal->submitter_id !== $user->id) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus proposal ini');

            return;
        }

        try {
            $proposal->delete();
            session()->flash('success', 'Proposal berhasil dihapus');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus proposal: '.$e->getMessage());
        }
    }
}
