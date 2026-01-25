<?php

namespace App\Observers;

use App\Models\Proposal;
use App\Models\ProposalActivity;
use App\Models\ProposalStatusLog;

class ProposalObserver
{
    /**
     * Handle the Proposal "created" event.
     */
    public function created(Proposal $proposal): void
    {
        ProposalActivity::create([
            'proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'activity_type' => 'created',
            'description' => 'Proposal dibuat',
        ]);
    }

    /**
     * Handle the Proposal "updated" event.
     */
    public function updated(Proposal $proposal): void
    {
        // Log status change to existing StatusLog
        if ($proposal->isDirty('status')) {
            ProposalStatusLog::create([
                'proposal_id' => $proposal->id,
                'user_id' => auth()->id(),
                'status_before' => $proposal->getOriginal('status'),
                'status_after' => $proposal->status,
                'notes' => $proposal->notes ?? null,
                'at' => now(),
            ]);
        }

        // Log all changes to ActivityLog
        $changes = [];
        $ignoredFields = ['updated_at', 'status']; // status already logged in StatusLog

        foreach ($proposal->getChanges() as $field => $newValue) {
            if (in_array($field, $ignoredFields)) {
                continue;
            }

            $changes[$field] = [
                'old' => $proposal->getOriginal($field),
                'new' => $newValue,
            ];
        }

        if (! empty($changes)) {
            ProposalActivity::create([
                'proposal_id' => $proposal->id,
                'user_id' => auth()->id(),
                'activity_type' => 'updated',
                'description' => 'Perubahan data proposal',
                'changes' => $changes,
            ]);
        }
    }

    /**
     * Handle the Proposal "deleted" event.
     */
    public function deleted(Proposal $proposal): void
    {
        ProposalActivity::create([
            'proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'activity_type' => 'deleted',
            'description' => 'Proposal dihapus (Soft Delete)',
        ]);
    }

    /**
     * Handle the Proposal "restored" event.
     */
    public function restored(Proposal $proposal): void
    {
        ProposalActivity::create([
            'proposal_id' => $proposal->id,
            'user_id' => auth()->id(),
            'activity_type' => 'restored',
            'description' => 'Proposal dipulihkan',
        ]);
    }

    /**
     * Handle the Proposal "force deleted" event.
     */
    public function forceDeleted(Proposal $proposal): void
    {
        // No activity log here as the record is gone
    }
}
