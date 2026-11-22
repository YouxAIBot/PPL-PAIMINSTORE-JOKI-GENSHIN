<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BoosterController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeaderboardController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login']);

    Route::middleware('admin')->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('packages', PackageController::class)->except('show');

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('orders/{order}/assign', [OrderController::class, 'assignBooster'])->name('orders.assign');
        Route::post('orders/{order}/priority', [OrderController::class, 'updatePriority'])->name('orders.priority');
        Route::post('orders/{order}/credentials', [OrderController::class, 'showCredentials'])->name('orders.credentials');

        Route::get('boosters', [BoosterController::class, 'index'])->name('boosters.index');
        Route::get('boosters/{user}', [BoosterController::class, 'show'])->name('boosters.show');

        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
        Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
        Route::post('payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::post('users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
        Route::post('users/{user}/unban', [UserController::class, 'unban'])->name('users.unban');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        Route::get('chats', [ChatController::class, 'index'])->name('chats.index');
        Route::get('chats/{conversation}', [ChatController::class, 'show'])->name('chats.show');
        Route::post('chats/{conversation}', [ChatController::class, 'reply'])->name('chats.reply');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');
        Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');

        Route::get('leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
        Route::post('leaderboard', [LeaderboardController::class, 'store'])->name('leaderboard.store');

        Route::get('logs', [LogController::class, 'index'])->name('logs.index');
    });
});
