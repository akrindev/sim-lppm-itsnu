<?php

namespace App\Http\Controllers;

use App\Models\ProposalReviewer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReviewExportController extends Controller
{
    /**
     * Download the review result as PDF.
     */
    public function download(ProposalReviewer $proposalReviewer)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $proposal = $proposalReviewer->proposal;

        // Authorization check
        $isReviewer = $proposalReviewer->user_id === $user->id;
        $isLppm = $user->hasRole(['admin lppm', 'kepala lppm', 'superadmin', 'rektor']);

        if (! $isReviewer && ! $isLppm) {
            abort(403, 'Anda tidak memiliki akses untuk mengekspor penilaian ini.');
        }

        if (! $proposalReviewer->isCompleted()) {
            abort(400, 'Penilaian belum diselesaikan.');
        }

        $proposal->load([
            'submitter.identity.faculty',
            'submitter.identity.studyProgram',
            'teamMembers',
        ]);

        $proposalReviewer->load('user.identity');

        $scores = $proposalReviewer->scores()
            ->where('round', $proposalReviewer->round)
            ->with('criteria')
            ->get();

        $totalScore = $scores->sum('value');

        $type = $proposal->detailable_type === 'App\Models\Research' ? 'research' : 'community_service';

        $pdf = Pdf::loadView('pdf.review-evaluation', [
            'assignment' => $proposalReviewer,
            'proposal' => $proposal,
            'scores' => $scores,
            'totalScore' => $totalScore,
            'type' => $type,
        ]);

        $filename = 'Penilaian_Reviewer_'.str_replace(' ', '_', substr($proposal->title, 0, 30)).'.pdf';

        return $pdf->download($filename);
    }
}
