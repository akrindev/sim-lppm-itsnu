<?php

use App\Http\Controllers\RoleSwitcherController;
use App\Livewire\Dashboard;
use App\Livewire\Dekan\ProposalIndex as DekanProposalIndex;
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

Route::redirect('/', 'dashboard', 302);

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('laporan-penelitian', \App\Livewire\Reports\Research::class)
        ->middleware(['role:admin lppm|rektor|kepala lppm'])
        ->name('reports.research');

    Route::get('laporan-luaran', \App\Livewire\Reports\OutputReports::class)
        ->middleware(['role:admin lppm|rektor|kepala lppm'])
        ->name('reports.outputs');

    // User Management Routes
    Route::middleware(['role:admin lppm'])->prefix('users')->name('users.')->group(function () {
        Route::get('/', UsersIndex::class)->name('index');
        Route::get('import', \App\Livewire\Users\Import::class)->name('import');
        Route::get('create', UsersCreate::class)->name('create');
        Route::get('{user}', UsersShow::class)->name('show');
        Route::get('{user}/edit', UsersEdit::class)->name('edit');
    });

    // Research Routes
    Route::middleware(['role:dosen|kepala lppm|reviewer|admin lppm|rektor|dekan'])->prefix('research')->name('research.')->group(function () {
        Route::get('/', \App\Livewire\Research\Proposal\Index::class)->name('proposal.index');

        // Only dosen can create proposals
        Route::get('proposal/create', \App\Livewire\Research\Proposal\Create::class)
            ->middleware('role:dosen')
            ->name('proposal.create');

        Route::get('proposal/{proposal}', \App\Livewire\Research\Proposal\Show::class)->name('proposal.show');
        Route::get('proposal/{proposal}/edit', \App\Livewire\Research\Proposal\Edit::class)->name('proposal.edit');

        Route::get('proposal-revision', \App\Livewire\Research\ProposalRevision\Index::class)->name('proposal-revision.index');
        Route::get('proposal-revision/{proposal}', \App\Livewire\Research\ProposalRevision\Show::class)->name('proposal-revision.show');

        Route::get('progress-report', \App\Livewire\Research\ProgressReport\Index::class)->name('progress-report.index');
        Route::get('progress-report/{proposal}', \App\Livewire\Reports\Show::class)
            ->name('progress-report.show')
            ->defaults('type', 'research-progress');

        Route::get('final-report', \App\Livewire\Research\FinalReport\Index::class)->name('final-report.index');
        Route::get('final-report/{proposal}', \App\Livewire\Research\FinalReport\Show::class)
            ->name('final-report.show');

        Route::get('daily-note', \App\Livewire\Research\DailyNote\Index::class)->name('daily-note.index');
        Route::get('daily-note/{proposal}', \App\Livewire\Research\DailyNote\Show::class)->name('daily-note.show');
    });

    // Community Service Routes
    Route::middleware(['role:dosen|kepala lppm|reviewer|admin lppm|rektor|dekan'])->prefix('community-service')->name('community-service.')->group(function () {
        Route::get('/', \App\Livewire\CommunityService\Proposal\Index::class)->name('proposal.index');

        // Only dosen can create proposals
        Route::get('proposal/create', \App\Livewire\CommunityService\Proposal\Create::class)
            ->middleware('role:dosen')
            ->name('proposal.create');

        Route::get('proposal/{proposal}', \App\Livewire\CommunityService\Proposal\Show::class)->name('proposal.show');
        Route::get('proposal/{proposal}/edit', \App\Livewire\CommunityService\Proposal\Edit::class)->name('proposal.edit');

        Route::get('proposal-revision', \App\Livewire\CommunityService\ProposalRevision\Index::class)->name('proposal-revision.index');
        Route::get('proposal-revision/{proposal}', \App\Livewire\CommunityService\ProposalRevision\Show::class)->name('proposal-revision.show');

        Route::get('progress-report', \App\Livewire\CommunityService\ProgressReport\Index::class)->name('progress-report.index');
        Route::get('progress-report/{proposal}', \App\Livewire\Reports\Show::class)
            ->name('progress-report.show')
            ->defaults('type', 'community-service-progress');

        Route::get('final-report', \App\Livewire\CommunityService\FinalReport\Index::class)->name('final-report.index');
        Route::get('final-report/{proposal}', \App\Livewire\CommunityService\FinalReport\Show::class)
            ->name('final-report.show');

        Route::get('daily-note', \App\Livewire\CommunityService\DailyNote\Index::class)->name('daily-note.index');
        Route::get('daily-note/{proposal}', \App\Livewire\CommunityService\DailyNote\Show::class)->name('daily-note.show');
    });

    // Dekan Routes
    Route::middleware(['role:dekan'])->prefix('dekan')->name('dekan.')->group(function () {
        Route::get('proposals', DekanProposalIndex::class)->name('proposals.index');
        Route::get('riwayat-persetujuan', \App\Livewire\Dekan\ApprovalHistory::class)->name('approval-history');
    });

    // Review Routes
    Route::middleware(['role:reviewer'])->prefix('review')->name('review.')->group(function () {
        Route::get('research', ReviewResearch::class)->name('research');
        Route::get('community-service', ReviewCommunityService::class)->name('community-service');
        Route::get('riwayat-review', \App\Livewire\Review\ReviewHistory::class)->name('review-history');
    });

    // Kepala LPPM Routes
    Route::middleware(['role:kepala lppm|rektor'])->prefix('kepala-lppm')->name('kepala-lppm.')->group(function () {
        Route::get('persetujuan-awal', \App\Livewire\KepalaLppm\InitialApproval::class)->name('initial-approval');
        Route::get('persetujuan-akhir', \App\Livewire\KepalaLppm\FinalDecision::class)->name('final-decision');
    });

    // Admin LPPM Routes
    Route::middleware(['role:admin lppm'])->prefix('admin-lppm')->name('admin-lppm.')->group(function () {
        Route::get('penugasan-reviewer', \App\Livewire\AdminLppm\ReviewerAssignment::class)->name('assign-reviewers');
        Route::get('beban-kerja-reviewer', \App\Livewire\AdminLppm\ReviewerWorkload::class)->name('reviewer-workload');
        Route::get('monitoring-review', \App\Livewire\AdminLppm\ReviewMonitoring::class)->name('review-monitoring');
    });

    Route::get('settings', SettingsIndex::class)
        ->middleware(['auth', 'verified'])
        ->name('settings');

    Route::redirect('settings/profile', '/settings')->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)
        ->middleware(['role:admin lppm'])
        ->name('settings.appearance');

    Route::middleware(['role:admin lppm'])->group(function () {
        Route::get('settings/master-data', MasterData::class)->name('settings.master-data');
        Route::get('settings/proposal-schedule', \App\Livewire\Settings\ProposalSchedule::class)->name('settings.proposal-schedule');
        Route::get('settings/proposal-template', \App\Livewire\Settings\ProposalTemplate::class)->name('settings.proposal-template');
    });

    Route::get('settings/two-factor', TwoFactor::class)
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
    Route::get('notifications', NotificationCenter::class)
        ->middleware(['auth', 'verified'])
        ->name('notifications');

    // Role Switcher Route
    Route::post('role/switch', [RoleSwitcherController::class, 'switch'])
        ->name('role.switch');
});

require __DIR__.'/auth.php';
