<?php

use App\Livewire\CommunityService\DailyNote\Index as CommunityServiceDailyNoteIndex;
use App\Livewire\CommunityService\FinalReport\Index as CommunityServiceFinalReportIndex;
use App\Livewire\CommunityService\Index as CommunityServiceIndex;
use App\Livewire\CommunityService\ProgressReport\Index as CommunityServiceProgressReportIndex;
use App\Livewire\CommunityService\Proposal\Create as CommunityServiceProposalCreate;
use App\Livewire\CommunityService\Proposal\Edit as CommunityServiceProposalEdit;
use App\Livewire\CommunityService\Proposal\Index as CommunityServiceProposalIndex;
use App\Livewire\CommunityService\Proposal\Show as CommunityServiceProposalShow;
use App\Livewire\CommunityService\ProposalRevision\Index as CommunityServiceProposalRevisionIndex;
use App\Livewire\Research\DailyNote\Index as ResearchDailyNoteIndex;
use App\Livewire\Research\FinalReport\Index as ResearchFinalReportIndex;
use App\Livewire\Research\ProgressReport\Index as ResearchProgressReportIndex;
use App\Livewire\Research\Proposal\Create as ResearchProposalCreate;
use App\Livewire\Research\Proposal\Edit as ResearchProposalEdit;
use App\Livewire\Research\Proposal\Index as ResearchProposalIndex;
use App\Livewire\Research\Proposal\Show as ResearchProposalShow;
use App\Livewire\Research\ProposalRevision\Index as ResearchProposalRevisionIndex;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Users\Create as UsersCreate;
use App\Livewire\Users\Edit as UsersEdit;
use App\Livewire\Users\Index as UsersIndex;
use App\Livewire\Users\Show as UsersShow;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
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
    Route::middleware(['role:dosen'])->prefix('research')->name('research.')->group(function () {
        Route::get('/', ResearchProposalIndex::class)->name('proposal.index');

        Route::get('proposal/create', ResearchProposalCreate::class)->name('proposal.create');
        Route::get('proposal/{proposal}', ResearchProposalShow::class)->name('proposal.show');
        Route::get('proposal/{proposal}/edit', ResearchProposalEdit::class)->name('proposal.edit');
        Route::get('proposal-revision', ResearchProposalRevisionIndex::class)->name('proposal-revision.index');
        Route::get('progress-report', ResearchProgressReportIndex::class)->name('progress-report.index');
        Route::get('final-report', ResearchFinalReportIndex::class)->name('final-report.index');
        Route::get('daily-note', ResearchDailyNoteIndex::class)->name('daily-note.index');
    });

    // Community Service Routes
    Route::middleware(['role:dosen'])->prefix('community-service')->name('community-service.')->group(function () {
        Route::get('/', CommunityServiceProposalIndex::class)->name('proposal.index');
        Route::get('proposal/create', CommunityServiceProposalCreate::class)->name('proposal.create');
        Route::get('proposal/{proposal}', CommunityServiceProposalShow::class)->name('proposal.show');
        Route::get('proposal/{proposal}/edit', CommunityServiceProposalEdit::class)->name('proposal.edit');
        Route::get('proposal-revision', CommunityServiceProposalRevisionIndex::class)->name('proposal-revision.index');
        Route::get('progress-report', CommunityServiceProgressReportIndex::class)->name('progress-report.index');
        Route::get('final-report', CommunityServiceFinalReportIndex::class)->name('final-report.index');
        Route::get('daily-note', CommunityServiceDailyNoteIndex::class)->name('daily-note.index');
    });

    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

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
});

require __DIR__ . '/auth.php';
