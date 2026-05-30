<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\PaymentController as AdminPayment;
use App\Http\Controllers\Admin\LotteryController as AdminLottery;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncement;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\User\WalletController as UserWallet;
use App\Http\Controllers\User\LotteryController as UserLottery;
use App\Http\Controllers\User\VideoController as UserVideo;

// ── Auth ──────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login',   [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register',[RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ── Admin Routes ──────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::get('/payments',                      [AdminPayment::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}',            [AdminPayment::class, 'show'])->name('payments.show');
    Route::post('/payments/{payment}/approve',   [AdminPayment::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject',    [AdminPayment::class, 'reject'])->name('payments.reject');

    Route::get('/lotteries',                            [AdminLottery::class, 'index'])->name('lotteries.index');
    Route::get('/lotteries/create',                     [AdminLottery::class, 'create'])->name('lotteries.create');
    Route::post('/lotteries',                           [AdminLottery::class, 'store'])->name('lotteries.store');
    Route::get('/lotteries/{lottery}',                  [AdminLottery::class, 'show'])->name('lotteries.show');
    Route::post('/lotteries/{lottery}/announce-winner', [AdminLottery::class, 'announceWinner'])->name('lotteries.announce-winner');

    Route::get('/users',                [AdminUser::class, 'index'])->name('users.index');
    Route::get('/users/{user}',         [AdminUser::class, 'show'])->name('users.show');
    Route::post('/users/{user}/toggle', [AdminUser::class, 'toggleStatus'])->name('users.toggle');

    Route::get('/announcements',                         [AdminAnnouncement::class, 'index'])->name('announcements.index');
    Route::get('/announcements/create',                  [AdminAnnouncement::class, 'create'])->name('announcements.create');
    Route::post('/announcements',                        [AdminAnnouncement::class, 'store'])->name('announcements.store');
    Route::post('/announcements/{announcement}/destroy', [AdminAnnouncement::class, 'destroy'])->name('announcements.destroy');
});

// ── User Routes ───────────────────────────────────────────────
Route::prefix('user')->name('user.')->middleware(['auth', 'verified.user'])->group(function () {
    Route::get('/dashboard', [UserDashboard::class, 'index'])->name('dashboard');

    Route::get('/wallet',          [UserWallet::class, 'index'])->name('wallet.index');
    Route::post('/wallet/deposit', [UserWallet::class, 'requestDeposit'])->name('wallet.deposit');

    Route::get('/lotteries',                [UserLottery::class, 'index'])->name('lotteries.index');
    Route::get('/lotteries/{lottery}',      [UserLottery::class, 'show'])->name('lotteries.show');
    Route::post('/lotteries/{lottery}/buy', [UserLottery::class, 'buy'])->name('lotteries.buy');

    Route::post('/video/{draw}/viewed', [UserVideo::class, 'markViewed'])->name('video.viewed');
    Route::get('/video/{draw}/active',  [UserVideo::class, 'checkActive'])->name('video.active');
});

// ── Root Redirect ─────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }
    return redirect()->route('login');
});