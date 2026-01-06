<?php

namespace App\Livewire\Traits;

use App\Models\Proposal;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

trait WithTeamManagement
{
    protected function teamNotificationService(): NotificationService
    {
        return app(NotificationService::class);
    }

    public function acceptMember(string $userId): void
    {
        $proposal = $this->getProposal();

        DB::transaction(function () use ($proposal, $userId) {
            $proposal->teamMembers()
                ->wherePivot('user_id', $userId)
                ->update(['status' => 'accepted']);

            $member = $proposal->teamMembers()->find($userId);
            $this->teamNotificationService()->notifyTeamInvitationAccepted(
                $proposal,
                $proposal->submitter,
                $member
            );
        });
    }

    public function rejectMember(string $userId): void
    {
        $proposal = $this->getProposal();

        DB::transaction(function () use ($proposal, $userId) {
            $proposal->teamMembers()
                ->wherePivot('user_id', $userId)
                ->update(['status' => 'rejected']);

            $member = $proposal->teamMembers()->find($userId);
            $this->teamNotificationService()->notifyTeamInvitationRejected(
                $proposal,
                $proposal->submitter,
                $member
            );

            if ($proposal->hasRejectedMembers()) {
                $proposal->update(['status' => 'need_assignment']);
            }
        });
    }

    abstract protected function getProposal(): Proposal;
}
