<?php

use App\Enums\Role;
use App\Http\Controllers\Customer\OrderingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Management\InventoryController;
use App\Http\Controllers\Management\MenuController;
use App\Http\Controllers\Management\OrderController;
use App\Http\Controllers\Management\ProfileController;
use App\Http\Controllers\Management\ReportController;
use App\Http\Controllers\Management\SettingController;
use App\Http\Controllers\Management\TableController;
use App\Http\Controllers\Management\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PublicSiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicSiteController::class, 'home'])->name('public.home');
Route::get('/about', [PublicSiteController::class, 'about'])->name('public.about');
Route::get('/pesan-menu', [PublicSiteController::class, 'menu'])->name('public.menu');
Route::get('/contacts', [PublicSiteController::class, 'contact'])->name('public.contact');
Route::get('/qr/{token}', [OrderingController::class, 'showMenu'])->name('customer.table');
Route::get('/qr/{token}/checkout', [OrderingController::class, 'showCheckout'])->name('customer.checkout');
Route::post('/qr/{token}/orders', [OrderingController::class, 'store'])->name('customer.order.store');
Route::get('/my-orders', [OrderingController::class, 'history'])->name('customer.history');
Route::get('/tracking/{publicId}', [OrderingController::class, 'track'])->name('customer.track');
Route::post('/tracking/{publicId}/feedback', [OrderingController::class, 'feedback'])->name('customer.feedback');
Route::get('/orders/{order}/payment', [PaymentController::class, 'show'])->name('orders.payment.show');
Route::post('/orders/{order}/verify-payment', [PaymentController::class, 'verify'])->name('orders.payment.verify');
Route::post('/orders/{order}/regenerate-token', [PaymentController::class, 'regenerateToken'])->name('orders.payment.regenerate');

Route::middleware(['auth'])->group(function (): void {
    // Akses Dasar (Semua Role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/overview', [DashboardController::class, 'overview'])->name('dashboard.overview');
    Route::get('/dashboard/orders', [DashboardController::class, 'orders'])->name('dashboard.orders');
    Route::get('/dashboard/inventory', [DashboardController::class, 'inventory'])->name('dashboard.inventory');
    Route::get('/dashboard/profile', [DashboardController::class, 'profile'])->name('dashboard.profile');
    Route::patch('/dashboard/profile', [ProfileController::class, 'update'])->name('dashboard.profile.update');

    // Akses Admin & SuperAdmin
    Route::middleware('role:' . Role::SuperAdmin->value . ',' . Role::Admin->value)->group(function (): void {
        Route::get('/dashboard/tables', [DashboardController::class, 'tables'])->name('dashboard.tables');
        Route::get('/dashboard/reports', [ReportController::class, 'index'])->name('dashboard.reports');
        Route::get('/dashboard/web-content', [DashboardController::class, 'webContent'])->name('dashboard.web-content');


        Route::patch('/management/web-content', [SettingController::class, 'updateContent'])->name('management.web-content.update');

        Route::post('/management/tables', [TableController::class, 'store'])->name('management.tables.store');
        Route::patch('/management/tables/{table}', [TableController::class, 'update'])->name('management.tables.update');
        Route::delete('/management/tables/{table}', [TableController::class, 'destroy'])->name('management.tables.destroy');
        Route::post('/management/menu-categories', [MenuController::class, 'storeCategory'])->name('management.menu.category.store');
        Route::post('/management/menu-items', [MenuController::class, 'store'])->name('management.menu.store');
        Route::post('/management/menu-items/{menuItem}', [MenuController::class, 'update'])->name('management.menu.update');
        Route::patch('/management/menu-items/{menuItem}/availability', [MenuController::class, 'toggleAvailability'])->name('management.menu.toggle');
        Route::get('/management/reports/weekly-export', [ReportController::class, 'exportWeekly'])->name('management.reports.export');
        Route::get('/management/reports/pdf', [ReportController::class, 'exportPdf'])->name('management.reports.pdf');

    });

    // Akses SuperAdmin Saja
    Route::middleware('role:' . Role::SuperAdmin->value)->group(function (): void {
        Route::get('/dashboard/settings', [DashboardController::class, 'settings'])->name('dashboard.settings');
        Route::patch('/management/settings', [SettingController::class, 'update'])->name('management.settings.update');

        // Kelola Staf (User Management) HANYA SuperAdmin
        Route::get('/dashboard/users', [UserController::class, 'index'])->name('dashboard.users');
        Route::post('/management/users', [UserController::class, 'store'])->name('management.users.store');
        Route::patch('/management/users/{user}', [UserController::class, 'update'])->name('management.users.update');
        Route::delete('/management/users/{user}', [UserController::class, 'destroy'])->name('management.users.destroy');
    });

    // Akses Operasional (Semua Role Staf)
    Route::middleware('role:' . Role::SuperAdmin->value . ',' . Role::Admin->value . ',' . Role::Employee->value)->group(function (): void {
        Route::post('/management/inventory/{ingredient}/adjust', [InventoryController::class, 'adjust'])->name('management.inventory.adjust');
        Route::post('/management/orders', [OrderController::class, 'store'])->name('management.orders.store');
        Route::patch('/management/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('management.orders.status');
    });
});
