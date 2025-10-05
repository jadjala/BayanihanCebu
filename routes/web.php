<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public/Guest Routes (Define FIRST before any auth middleware)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Authentication routes (guest only) - MUST be defined before auth middleware
Route::middleware('guest')->group(function () {
    Route::get('/register', [UserController::class, 'showRegister'])->name('register');
    Route::post('/register', [UserController::class, 'register']);
    Route::get('/login', [UserController::class, 'showLogin'])->name('login');
    Route::post('/login', [UserController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    
    // Logout
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    
    // Dashboard (all authenticated users)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Profile routes
    Route::get('/profile', [UserController::class, 'profile'])->name('profile.edit');
    Route::patch('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    
    // Incident Report routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [IncidentController::class, 'index'])->name('index');
        Route::get('/create', [IncidentController::class, 'create'])->name('create');
        Route::post('/', [IncidentController::class, 'store'])->name('store');
        Route::get('/{report}', [IncidentController::class, 'show'])->name('show');
        Route::get('/{report}/edit', [IncidentController::class, 'edit'])->name('edit');
        Route::patch('/{report}', [IncidentController::class, 'update'])->name('update');
        Route::delete('/{report}', [IncidentController::class, 'destroy'])->name('destroy');
        
        // Official/Admin only: Verify reports
        Route::post('/{report}/verify', [IncidentController::class, 'verify'])
            ->name('verify')
            ->middleware('role:official,admin');
    });
    
    // Donation routes
    Route::prefix('donations')->name('donations.')->group(function () {
        Route::get('/', [DonationController::class, 'index'])->name('index');
        Route::get('/create', [DonationController::class, 'create'])->name('create');
        Route::post('/', [DonationController::class, 'store'])->name('store');
        Route::get('/{donation}', [DonationController::class, 'show'])->name('show');
        Route::get('/{donation}/edit', [DonationController::class, 'edit'])->name('edit');
        Route::patch('/{donation}', [DonationController::class, 'update'])->name('update');
        Route::delete('/{donation}', [DonationController::class, 'destroy'])->name('destroy');
        
        // Donation history/transparency ledger
        Route::get('/{donation}/history', [DonationController::class, 'history'])->name('history');
        
        // Official/Admin only: Verify and distribute donations
        Route::post('/{donation}/verify', [DonationController::class, 'verify'])
            ->name('verify')
            ->middleware('role:official,admin');
        Route::get('/{donation}/distribute', [DonationController::class, 'showDistribute'])
            ->name('distribute.show')
            ->middleware('role:official,admin');
        Route::post('/{donation}/distribute', [DonationController::class, 'distribute'])
            ->name('distribute')
            ->middleware('role:official,admin');
    });
    
    // Notification routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        
        // API endpoint for live notification count
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
    });
    
    // Export routes (Official/Admin only)
    Route::middleware('role:official,admin')->group(function () {
        Route::get('/reports/export/csv', [DashboardController::class, 'exportReports'])->name('reports.export');
        Route::get('/donations/export/csv', [DashboardController::class, 'exportDonations'])->name('donations.export');
    });
    
    // Admin-only routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
    });
});