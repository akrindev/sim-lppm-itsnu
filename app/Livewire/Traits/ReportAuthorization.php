<?php

declare(strict_types=1);

namespace App\Livewire\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\EloquentBuilder;

trait ReportAuthorization
{
    protected function filterByUserAccess(Builder $query): Builder
    {
        $user = Auth::user();

        return $query->where(function ($q) use ($user) {
            $q->where('submitter_id', $user->id)
                ->orWhereHas('teamMembers', function ($subQuery) use ($user) {
                    $subQuery->where('user_id', $user->id)
                        ->where('status', 'accepted');
                });
        });
    }

    protected function canEditReport(Model $report): bool
    {
        $user = Auth::user();

        return $report->proposal->submitter_id === $user->id
            || $report->proposal->teamMembers()
                ->where('user_id', $user->id)
                ->where('status', 'accepted')
                ->exists();
    }
}
