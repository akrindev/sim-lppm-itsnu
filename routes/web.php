<?php

use App\Http\Controllers\RoleSwitcherController;
use App\Livewire\CommunityService\DailyNote\Index as CommunityServiceDailyNoteIndex;
use App\Livewire\CommunityService\FinalReport\Index as CommunityServiceFinalReportIndex;
use App\Livewire\CommunityService\ProgressReport\Index as CommunityServiceProgressReportIndex;
use App\Livewire\CommunityService\Proposal\Create as CommunityServiceProposalCreate;
use App\Livewire\CommunityService\Proposal\Edit as CommunityServiceProposalEdit;
use App\Livewire\CommunityService\Proposal\Index as CommunityServiceProposalIndex;
use App\Livewire\CommunityService\Proposal\Show as CommunityServiceProposalShow;
use App\Livewire\CommunityService\ProposalRevision\Index as CommunityServiceProposalRevisionIndex;
use App\Livewire\CommunityService\ProposalRevision\Show as CommunityServiceProposalRevisionShow;
use App\Livewire\Dashboard;
use App\Livewire\Dekan\ProposalIndex as DekanProposalIndex;
use App\Livewire\Notifications\NotificationCenter;
use App\Livewire\Research\DailyNote\Index as ResearchDailyNoteIndex;
use App\Livewire\Research\FinalReport\Index as ResearchFinalReportIndex;
use App\Livewire\Research\ProgressReport\Index as ResearchProgressReportIndex;
use App\Livewire\Research\ProgressReport\Show as ResearchProgressReportShow;
use App\Livewire\Research\Proposal\Create as ResearchProposalCreate;
use App\Livewire\Research\Proposal\Edit as ResearchProposalEdit;
use App\Livewire\Research\Proposal\Index as ResearchProposalIndex;
use App\Livewire\Research\Proposal\Show as ResearchProposalShow;
use App\Livewire\Research\ProposalRevision\Index as ResearchProposalRevisionIndex;
use App\Livewire\Research\ProposalRevision\Show as ResearchProposalRevisionShow;
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
    Route::view('laporan-penelitian', 'reports.research')
        ->middleware(['role:admin lppm|rektor'])
        ->name('reports.research');

    // User Management Routes
    Route::middleware(['role:admin lppm'])->prefix('users')->name('users.')->group(function () {
        Route::get('/', UsersIndex::class)->name('index');
        Route::get('create', UsersCreate::class)->name('create');
        Route::get('{user}', UsersShow::class)->name('show');
        Route::get('{user}/edit', UsersEdit::class)->name('edit');
    });

    // Research Routes
    Route::middleware(['role:dosen|kepala lppm|reviewer|admin lppm|rektor|dekan'])->prefix('research')->name('research.')->group(function () {
        Route::get('/', ResearchProposalIndex::class)->name('proposal.index');

        Route::get('proposal/create', ResearchProposalCreate::class)->name('proposal.create');
        Route::get('proposal/{proposal}', ResearchProposalShow::class)->name('proposal.show');
        Route::get('proposal/{proposal}/edit', ResearchProposalEdit::class)->name('proposal.edit');
        Route::get('proposal-revision', ResearchProposalRevisionIndex::class)->name('proposal-revision.index');
        Route::get('proposal-revision/{proposal}', ResearchProposalRevisionShow::class)->name('proposal-revision.show');
        Route::get('progress-report', ResearchProgressReportIndex::class)->name('progress-report.index');
        Route::get('progress-report/{proposal}', ResearchProgressReportShow::class)->name('progress-report.show');
        Route::get('final-report', ResearchFinalReportIndex::class)->name('final-report.index');
        Route::get('daily-note', ResearchDailyNoteIndex::class)->name('daily-note.index');
    });

    // Community Service Routes
    Route::middleware(['role:dosen|kepala lppm|reviewer|admin lppm|rektor|dekan'])->prefix('community-service')->name('community-service.')->group(function () {
        Route::get('/', CommunityServiceProposalIndex::class)->name('proposal.index');
        Route::get('proposal/create', CommunityServiceProposalCreate::class)->name('proposal.create');
        Route::get('proposal/{proposal}', CommunityServiceProposalShow::class)->name('proposal.show');
        Route::get('proposal/{proposal}/edit', CommunityServiceProposalEdit::class)->name('proposal.edit');
        Route::get('proposal-revision', CommunityServiceProposalRevisionIndex::class)->name('proposal-revision.index');
        Route::get('proposal-revision/{proposal}', CommunityServiceProposalRevisionShow::class)->name('proposal-revision.show');
        Route::get('progress-report', CommunityServiceProgressReportIndex::class)->name('progress-report.index');
        Route::get('final-report', CommunityServiceFinalReportIndex::class)->name('final-report.index');
        Route::get('daily-note', CommunityServiceDailyNoteIndex::class)->name('daily-note.index');
    });

    // Dekan Routes
    Route::middleware(['role:dekan'])->prefix('dekan')->name('dekan.')->group(function () {
        Route::get('proposals', DekanProposalIndex::class)->name('proposals.index');
    });

    // Review Routes
    Route::middleware(['role:reviewer'])->prefix('review')->name('review.')->group(function () {
        Route::get('research', ReviewResearch::class)->name('research');
        Route::get('community-service', ReviewCommunityService::class)->name('community-service');
    });

    Route::get('settings', SettingsIndex::class)
        ->middleware(['auth', 'verified'])
        ->name('settings');

    Route::redirect('settings/profile', '/settings')->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::middleware(['role:admin lppm'])->group(function () {
        Route::get('settings/master-data', MasterData::class)->name('settings.master-data');
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
