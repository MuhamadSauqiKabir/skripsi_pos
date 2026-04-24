<?php

use App\Enums\Role;
use App\Http\Controllers\Customer\OrderingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Management\InventoryController;
use App\Http\Controllers\Management\MenuController;
use App\Http\Controllers\Management\OrderController;
use App\Http\Controllers\Management\ReportController;
use App\Http\Controllers\Management\TableController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PublicSiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicSiteController::class, 'home'])->name('public.home');
Route::get('/about', [PublicSiteController::class, 'about'])->name('public.about');
Route::get('/store', [PublicSiteController::class, 'store'])->name('public.store');
Route::get('/pesan-menu', [PublicSiteController::class, 'menu'])->name('public.menu');
Route::get('/contacts', [PublicSiteController::class, 'contact'])->name('public.contact');
Route::get('/qr/{token}', [OrderingController::class, 'showMenu'])->name('customer.table');
Route::get('/qr/{token}/checkout', [OrderingController::class, 'showCheckout'])->name('customer.checkout');
Route::post('/qr/{token}/orders', [OrderingController::class, 'store'])->name('customer.order.store');
Route::get('/tracking/{publicId}', [OrderingController::class, 'track'])->name('customer.track');
Route::post('/tracking/{publicId}/feedback', [OrderingController::class, 'feedback'])->name('customer.feedback');
Route::get('/orders/{order}/payment', [PaymentController::class, 'show'])->name('orders.payment.show');

Route::middleware(['auth'])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/overview', [DashboardController::class, 'overview'])->name('dashboard.overview');
    Route::get('/dashboard/orders', [DashboardController::class, 'orders'])->name('dashboard.orders');
    Route::get('/dashboard/inventory', [DashboardController::class, 'inventory'])->name('dashboard.inventory');
    Route::get('/dashboard/tables', [DashboardController::class, 'tables'])->name('dashboard.tables');
    Route::get('/dashboard/reports', [DashboardController::class, 'reports'])->name('dashboard.reports');
    Route::get('/dashboard/profile', [DashboardController::class, 'profile'])->name('dashboard.profile');
    Route::get('/dashboard/settings', [DashboardController::class, 'settings'])->name('dashboard.settings');

    Route::middleware('role:'.Role::SuperAdmin->value.','.Role::Admin->value)->group(function (): void {
        Route::post('/management/tables', [TableController::class, 'store'])->name('management.tables.store');
        Route::post('/management/menu-items', [MenuController::class, 'store'])->name('management.menu.store');
        Route::patch('/management/menu-items/{menuItem}/availability', [MenuController::class, 'toggleAvailability'])->name('management.menu.toggle');
        Route::get('/management/reports/weekly-export', [ReportController::class, 'exportWeekly'])->name('management.reports.export');
    });

    Route::middleware('role:'.Role::SuperAdmin->value.','.Role::Admin->value.','.Role::Employee->value)->group(function (): void {
        Route::post('/management/inventory/{ingredient}/adjust', [InventoryController::class, 'adjust'])->name('management.inventory.adjust');
        Route::patch('/management/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('management.orders.status');
    });
});
