<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Component;

#[Layout('components.layouts.app', ['title' => 'User Details', 'pageTitle' => '', 'pageSubtitle' => 'User profile and metadata overview'])]
class Show extends Component
{
    public string $userId;

    /**
     * Boot the component with the selected user.
     */
    public function mount(User $user): void
    {
        $this->userId = $user->getKey();
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        $user = $this->user;

        return view('livewire.users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Resolve the currently viewed user.
     */
    /**
     * Resolve the currently viewed user.
     */
    #[Computed]
    public function user(): User
    {
        return User::query()
            ->with(['roles', 'identity.faculty', 'identity.institution', 'identity.studyProgram'])
            ->findOrFail($this->userId);
    }

    use \Livewire\WithPagination;

    #[Computed]
    public function researches()
    {
        $submitted = \App\Models\Proposal::select('proposals.*', \Illuminate\Support\Facades\DB::raw("'Ketua' as user_role"))
            ->where('submitter_id', $this->userId)
            ->where('detailable_type', 'App\Models\Research');

        $participated = \App\Models\Proposal::select('proposals.*', 'proposal_user.role as user_role')
            ->join('proposal_user', 'proposals.id', '=', 'proposal_user.proposal_id')
            ->where('proposal_user.user_id', $this->userId)
            ->where('detailable_type', 'App\Models\Research');

        return $submitted->union($participated)
            ->with(['researchScheme', 'statusLogs'])
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'researchPage');
    }

    #[Computed]
    public function communityServices()
    {
        $submitted = \App\Models\Proposal::select('proposals.*', \Illuminate\Support\Facades\DB::raw("'Ketua' as user_role"))
            ->where('submitter_id', $this->userId)
            ->where('detailable_type', 'App\Models\CommunityService');

        $participated = \App\Models\Proposal::select('proposals.*', 'proposal_user.role as user_role')
            ->join('proposal_user', 'proposals.id', '=', 'proposal_user.proposal_id')
            ->where('proposal_user.user_id', $this->userId)
            ->where('detailable_type', 'App\Models\CommunityService');

        return $submitted->union($participated)
            ->with(['researchScheme', 'statusLogs'])
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'pkmPage');
    }
}
