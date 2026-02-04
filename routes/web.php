<?php

use App\Http\Controllers\RoleSwitcherController;
use App\Livewire\Dashboard;
use App\Livewire\Dekan\ProposalIndex as DekanProposalIndex;
use App\Livewire\Installer\InstallerWizard;
use App\Livewire\Notifications\NotificationCenter;
use App\Livewire\Review\CommunityService as ReviewCommunityService;
use App\Livewire\Review\Research as ReviewResearch;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\MasterData;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\SettingsIndex;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Users\Create as UsersCreate;
use App\Livewire\Users\Edit as UsersEdit;
use App\Livewire\Users\Index as UsersIndex;
use App\Livewire\Users\Show as UsersShow;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// Installer Route - Available only when not installed
Route::livewire('install', InstallerWizard::class)
    ->name('install');

Route::redirect('/', 'dashboard', 302);

Route::livewire('dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::livewire('laporan-penelitian', \App\Livewire\Reports\Research::class)
        ->middleware(['role:admin lppm|rektor|kepala lppm'])
        ->name('reports.research');

    Route::livewire('laporan-pkm', \App\Livewire\Reports\CommunityService::class)
        ->middleware(['role:admin lppm|rektor|kepala lppm'])
        ->name('reports.pkm');

    Route::livewire('laporan-luaran', \App\Livewire\Reports\OutputReports::class)
        ->middleware(['role:admin lppm|rektor|kepala lppm'])
        ->name('reports.outputs');

    // User Management Routes
    Route::middleware(['role:admin lppm|superadmin'])->prefix('users')->name('users.')->group(function () {
        Route::livewire('/', UsersIndex::class)->name('index');
        Route::livewire('import', \App\Livewire\Users\Import::class)->name('import');
        Route::livewire('create', UsersCreate::class)->name('create');
        Route::livewire('{user}', UsersShow::class)->name('show');
        Route::livewire('{user}/edit', UsersEdit::class)->name('edit');
    });

    // Research Routes
    Route::middleware(['role:dosen|kepala lppm|reviewer|admin lppm|rektor|dekan'])->prefix('research')->name('research.')->group(function () {
        Route::livewire('/', \App\Livewire\Research\Proposal\Index::class)->name('proposal.index');

        // Only dosen can create proposals
        Route::livewire('proposal/create', \App\Livewire\Research\Proposal\Create::class)
            ->middleware('role:dosen')
            ->name('proposal.create');

        Route::livewire('proposal/{proposal}', \App\Livewire\Research\Proposal\Show::class)->name('proposal.show');
        Route::livewire('proposal/{proposal}/edit', \App\Livewire\Research\Proposal\Edit::class)->name('proposal.edit');

        Route::livewire('proposal-revision', \App\Livewire\Research\ProposalRevision\Index::class)->name('proposal-revision.index');
        Route::livewire('proposal-revision/{proposal}', \App\Livewire\Research\ProposalRevision\Show::class)->name('proposal-revision.show');

        Route::livewire('progress-report', \App\Livewire\Research\ProgressReport\Index::class)->name('progress-report.index');
        Route::livewire('progress-report/{proposal}', \App\Livewire\Reports\Show::class)
            ->name('progress-report.show')
            ->defaults('type', 'research-progress');

        Route::livewire('final-report', \App\Livewire\Research\FinalReport\Index::class)->name('final-report.index');
        Route::livewire('final-report/{proposal}', \App\Livewire\Research\FinalReport\Show::class)
            ->name('final-report.show');

        Route::livewire('daily-note', \App\Livewire\Research\DailyNote\Index::class)->name('daily-note.index');
        Route::livewire('daily-note/{proposal}', \App\Livewire\Research\DailyNote\Show::class)->name('daily-note.show');
    });

    // Community Service Routes
    Route::middleware(['role:dosen|kepala lppm|reviewer|admin lppm|rektor|dekan'])->prefix('community-service')->name('community-service.')->group(function () {
        Route::livewire('/', \App\Livewire\CommunityService\Proposal\Index::class)->name('proposal.index');

        // Only dosen can create proposals
        Route::livewire('proposal/create', \App\Livewire\CommunityService\Proposal\Create::class)
            ->middleware('role:dosen')
            ->name('proposal.create');

        Route::livewire('proposal/{proposal}', \App\Livewire\CommunityService\Proposal\Show::class)->name('proposal.show');
        Route::livewire('proposal/{proposal}/edit', \App\Livewire\CommunityService\Proposal\Edit::class)->name('proposal.edit');

        Route::livewire('proposal-revision', \App\Livewire\CommunityService\ProposalRevision\Index::class)->name('proposal-revision.index');
        Route::livewire('proposal-revision/{proposal}', \App\Livewire\CommunityService\ProposalRevision\Show::class)->name('proposal-revision.show');

        Route::livewire('progress-report', \App\Livewire\CommunityService\ProgressReport\Index::class)->name('progress-report.index');
        Route::livewire('progress-report/{proposal}', \App\Livewire\Reports\Show::class)
            ->name('progress-report.show')
            ->defaults('type', 'community-service-progress');

        Route::livewire('final-report', \App\Livewire\CommunityService\FinalReport\Index::class)->name('final-report.index');
        Route::livewire('final-report/{proposal}', \App\Livewire\CommunityService\FinalReport\Show::class)
            ->name('final-report.show');

        Route::livewire('daily-note', \App\Livewire\CommunityService\DailyNote\Index::class)->name('daily-note.index');
        Route::livewire('daily-note/{proposal}', \App\Livewire\CommunityService\DailyNote\Show::class)->name('daily-note.show');
    });

    // Dekan Routes
    Route::middleware(['role:dekan'])->prefix('dekan')->name('dekan.')->group(function () {
        Route::livewire('proposals', DekanProposalIndex::class)->name('proposals.index');
        Route::livewire('riwayat-persetujuan', \App\Livewire\Dekan\ApprovalHistory::class)->name('approval-history');
    });

    // Review Routes
    Route::middleware(['role:reviewer'])->prefix('review')->name('review.')->group(function () {
        Route::livewire('research', ReviewResearch::class)->name('research');
        Route::livewire('community-service', ReviewCommunityService::class)->name('community-service');
        Route::livewire('riwayat-review', \App\Livewire\Review\ReviewHistory::class)->name('review-history');
    });

    // Kepala LPPM Routes
    Route::middleware(['role:kepala lppm|rektor'])->prefix('kepala-lppm')->name('kepala-lppm.')->group(function () {
        Route::livewire('persetujuan-awal', \App\Livewire\KepalaLppm\InitialApproval::class)->name('initial-approval');
        Route::livewire('persetujuan-akhir', \App\Livewire\KepalaLppm\FinalDecision::class)->name('final-decision');
    });

    // Admin LPPM Routes
    Route::middleware(['role:admin lppm'])->prefix('admin-lppm')->name('admin-lppm.')->group(function () {
        Route::livewire('penugasan-reviewer', \App\Livewire\AdminLppm\ReviewerAssignment::class)->name('assign-reviewers');
        Route::livewire('beban-kerja-reviewer', \App\Livewire\AdminLppm\ReviewerWorkload::class)->name('reviewer-workload');
        Route::livewire('monitoring-review', \App\Livewire\AdminLppm\ReviewMonitoring::class)->name('review-monitoring');
        Route::livewire('monev', \App\Livewire\AdminLppm\Monev\MonevIndex::class)->name('monev.index');
    });

    Route::livewire('settings', SettingsIndex::class)
        ->middleware(['auth', 'verified'])
        ->name('settings');

    Route::redirect('settings/profile', '/settings')->name('settings.profile');
    Route::livewire('settings/password', Password::class)->name('settings.password');
    Route::livewire('settings/appearance', Appearance::class)
        ->middleware(['role:admin lppm|superadmin'])
        ->name('settings.appearance');

    Route::middleware(['role:admin lppm|superadmin'])->group(function () {
        Route::livewire('settings/master-data', MasterData::class)->name('settings.master-data');
        Route::livewire('settings/proposal-schedule', \App\Livewire\Settings\ProposalSchedule::class)->name('settings.proposal-schedule');
        Route::livewire('settings/proposal-template', \App\Livewire\Settings\ProposalTemplate::class)->name('settings.proposal-template');
    });

    Route::livewire('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // Notification Routes
    Route::livewire('notifications', NotificationCenter::class)
        ->middleware(['auth', 'verified'])
        ->name('notifications');

    // Role Switcher Route
    Route::post('role/switch', [RoleSwitcherController::class, 'switch'])
        ->name('role.switch');

    // PDF Export Route
    Route::get('proposals/{proposal}/export-pdf', [\App\Http\Controllers\ProposalExportController::class, 'download'])
        ->name('proposals.export-pdf');

    Route::get('reviewers/{proposalReviewer}/export-pdf', [\App\Http\Controllers\ReviewExportController::class, 'download'])
        ->name('reviewers.export-pdf');

    Route::get('daily-notes/{proposal}/export-pdf', [\App\Http\Controllers\DailyNoteExportController::class, 'download'])
        ->name('daily-notes.export-pdf');
});

require __DIR__.'/auth.php';
