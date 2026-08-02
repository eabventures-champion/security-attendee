<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Livewire\Dashboard\AdminDashboard;
use App\Livewire\Events\EventList;
use App\Livewire\Events\EventForm;
use App\Livewire\Events\EventDashboard;
use App\Livewire\Attendees\AttendeeList;
use App\Livewire\Gates\GateManager;
use App\Livewire\Scanner\ScannerDashboard;
use App\Livewire\PublicEventsList;
use App\Livewire\Registration\PrivateInvitationForm;
use App\Livewire\Registration\PublicRegistrationForm;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public Events Catalogue (Public Invitations)
Route::get('/public-events', PublicEventsList::class)->name('events.public.index');

// Public Event Registration
Route::get('/events/{event_slug}/register', PublicRegistrationForm::class)
    ->name('events.public.register');

// Private Event Invitation RSVP
Route::get('/events/{event_slug}/invite', PrivateInvitationForm::class)
    ->name('events.public.invite');

// Email Verification
Route::get('/verify/{token}', [App\Http\Controllers\VerificationController::class, 'verify'])
    ->name('verification.verify');

// Team Member Invitation Acceptance
Route::get('/team/accept-invite/{token}', [App\Http\Controllers\TeamInvitationController::class, 'acceptInvite'])
    ->name('team.accept_invite');

// Super Admin Organization Approval & Impersonation Endpoints
Route::get('/superadmin/approve-org/{token}', [App\Http\Controllers\SuperAdminApprovalController::class, 'approveOrg'])
    ->name('superadmin.approve_org');

Route::middleware('auth')->group(function () {
    Route::get('/superadmin/impersonate/{userId}', [App\Http\Controllers\SuperAdmin\ImpersonationController::class, 'impersonate'])
        ->name('superadmin.impersonate');
    Route::post('/superadmin/stop-impersonating', [App\Http\Controllers\SuperAdmin\ImpersonationController::class, 'stopImpersonating'])
        ->name('superadmin.stop_impersonate');
});

// Guest-only routes (login/register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', App\Livewire\Auth\Register::class)->name('register');

    // Password Reset
    Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

// Authenticated Routes
Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsActive::class])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

    // Events
    Route::get('/events', EventList::class)->name('events.index');
    Route::get('/events/create', EventForm::class)->name('events.create');
    Route::get('/events/{uuid}/edit', EventForm::class)->name('events.edit');
    Route::get('/events/{uuid}', EventDashboard::class)->name('events.show');

    // Attendees
    Route::get('/attendees/{eventUuid?}', AttendeeList::class)->name('attendees.index');
    Route::get('/events/{eventUuid}/attendees', AttendeeList::class);

    // Gates
    Route::get('/gates/{eventUuid?}', GateManager::class)->name('gates.index');
    Route::get('/events/{eventUuid}/gates', GateManager::class);

    // Scanner
    Route::get('/scanner/{eventUuid?}', ScannerDashboard::class)->name('scanner.index');
    Route::get('/events/{eventUuid}/scanner/{gateUuid?}', ScannerDashboard::class)->name('scanner.gate');
    Route::get('/events/{eventUuid}/scanner', ScannerDashboard::class);

    // Reports
    Route::get('/reports/{eventUuid?}', App\Livewire\Reports\EventReportView::class)->name('reports.index');
    Route::get('/events/{eventUuid}/reports', App\Livewire\Reports\EventReportView::class);

    // Resource Center & Support
    Route::get('/resources', App\Livewire\Resources\ResourceIndex::class)->name('resources.index');

    // Settings
    Route::get('/settings', App\Livewire\Settings\OrganizationSettings::class)->name('settings.index');

    // Profile
    Route::get('/profile', App\Livewire\Profile\ProfileForm::class)->name('profile.index');

    // Users & Roles Management (Phase 2)
    Route::get('/users', App\Livewire\Users\UserManager::class)->name('users.index');
});
