<?php

namespace App\Observers;

use App\Models\Proposal;
use App\Models\ProposalStatusLog;

class ProposalObserver
{
    /**
     * Handle the Proposal "created" event.
     */
    public function created(Proposal $proposal): void
    {
        //
    }

    /**
     * Handle the Proposal "updated" event.
     */
    public function updated(Proposal $proposal): void
    {
        if ($proposal->isDirty('status')) {
            ProposalStatusLog::create([
                'proposal_id' => $proposal->id,
                'user_id' => auth()->id(),
                'status_before' => $proposal->getOriginal('status'),
                'status_after' => $proposal->status,
                'at' => now(),
            ]);
        }
    }

    /**
     * Handle the Proposal "deleted" event.
     */
    public function deleted(Proposal $proposal): void
    {
        //
    }

    /**
     * Handle the Proposal "restored" event.
     */
    public function restored(Proposal $proposal): void
    {
        //
    }

    /**
     * Handle the Proposal "force deleted" event.
     */
    public function forceDeleted(Proposal $proposal): void
    {
        //
    }
}
