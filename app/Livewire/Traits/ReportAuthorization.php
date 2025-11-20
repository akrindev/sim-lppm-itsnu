<?php

declare(strict_types=1);

namespace App\Livewire\Traits;

use App\Models\Proposal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait ReportAuthorization
{
    protected function filterByUserAccess(Builder $query): Builder
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['admin lppm', 'kepala lppm', 'rektor'])) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('submitter_id', $user->id)
                ->orWhereHas('teamMembers', function ($subQuery) use ($user) {
                    $subQuery->where('user_id', $user->id)
                        ->where('status', 'accepted');
                });
        });
    }

    protected function canEditReport(Proposal $proposal): bool
    {
        $user = Auth::user();

        return $proposal->submitter_id === $user->id
            || $proposal->teamMembers()
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->exists();
    }
}
