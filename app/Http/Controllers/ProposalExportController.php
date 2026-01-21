<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Services\ProposalPdfService;
use Illuminate\Support\Facades\Auth;

class ProposalExportController extends Controller
{
    public function __construct(
        protected ProposalPdfService $pdfService
    ) {}

    /**
     * Download the combined proposal PDF.
     */
    public function download(Proposal $proposal)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $isMember = $proposal->teamMembers()->where('users.id', $user->id)->exists();
        $isSubmitter = $proposal->submitter_id === $user->id;
        $isLppm = $user->hasRole(['admin lppm', 'kepala lppm', 'superadmin', 'rektor', 'dekan']);
        $isAssignedReviewer = $proposal->reviewers()->where('user_id', $user->id)->exists();

        if (! $isSubmitter && ! $isMember && ! $isLppm && ! $isAssignedReviewer) {
            abort(403, 'Anda tidak memiliki akses untuk mengekspor proposal ini.');
        }

        $pdfPath = $this->pdfService->export($proposal);

        $filename = 'Proposal_'.str_replace(' ', '_', substr($proposal->title, 0, 50)).'.pdf';

        return response()->download($pdfPath, $filename)->deleteFileAfterSend(true);
    }
}
