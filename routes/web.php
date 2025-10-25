<?php

use App\Livewire\CommunityService\DailyNote\Index as CommunityServiceDailyNoteIndex;
use App\Livewire\CommunityService\FinalReport\Index as CommunityServiceFinalReportIndex;
use App\Livewire\CommunityService\Index as CommunityServiceIndex;
use App\Livewire\CommunityService\ProgressReport\Index as CommunityServiceProgressReportIndex;
use App\Livewire\CommunityService\Proposal\Create as CommunityServiceProposalCreate;
use App\Livewire\CommunityService\ProposalRevision\Index as CommunityServiceProposalRevisionIndex;
use App\Livewire\Research\DailyNote\Index as ResearchDailyNoteIndex;
use App\Livewire\Research\FinalReport\Index as ResearchFinalReportIndex;
use App\Livewire\Research\Proposal\Index as ResearchIndex;
use App\Livewire\Research\ProgressReport\Index as ResearchProgressReportIndex;
use App\Livewire\Research\Proposal\Create as ResearchProposalCreate;
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
    Route::view('proposals', 'proposals.index')->name('proposals.index');

    Route::view('laporan-penelitian', 'reports.research')
        ->middleware(['role:admin lppm|rektor'])
        ->name('reports.research');

    Route::get('users', UsersIndex::class)
        ->middleware(['role:admin lppm'])
        ->name('users.index');

    Route::get('users/create', UsersCreate::class)
        ->middleware(['role:admin lppm'])
        ->name('users.create');

    Route::get('users/{user}', UsersShow::class)
        ->middleware(['role:admin lppm'])
        ->name('users.show');

    Route::get('users/{user}/edit', UsersEdit::class)
        ->middleware(['role:admin lppm'])
        ->name('users.edit');

    // Research Routes
    Route::middleware(['role:dosen'])->prefix('research')->name('research.')->group(function () {
        Route::get('/', ResearchIndex::class)->name('proposal.index');

        Route::get('proposal/create', ResearchProposalCreate::class)->name('proposal.create');
        Route::get('proposal-revision', ResearchProposalRevisionIndex::class)->name('proposal-revision.index');
        Route::get('progress-report', ResearchProgressReportIndex::class)->name('progress-report.index');
        Route::get('final-report', ResearchFinalReportIndex::class)->name('final-report.index');
        Route::get('daily-note', ResearchDailyNoteIndex::class)->name('daily-note.index');
    });

    // Community Service Routes
    Route::middleware(['role:dosen'])->prefix('community-service')->name('community-service.')->group(function () {
        Route::get('/', CommunityServiceIndex::class)->name('index');
        Route::get('proposal/create', CommunityServiceProposalCreate::class)->name('proposal.create');
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
