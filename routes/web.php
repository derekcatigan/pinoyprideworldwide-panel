<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\ContainerListController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ReceiveOrderController;
use App\Http\Controllers\Admin\TrackingStatusController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Branch\BranchAccountController;
use App\Http\Controllers\Branch\BranchDashboardController;
use App\Http\Controllers\Branch\CollectedOrderController;
use App\Http\Controllers\Branch\LoadContainerController;
use App\Http\Controllers\Shared\BoxController;
use App\Http\Controllers\Shared\ContainerController;
use App\Http\Controllers\Shared\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;


Route::middleware('guest')->group(function () {
    /* ------------------------- Handles the Login Route ------------------------ */
    Route::get('/', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('auth.login');
});


Route::middleware('auth')->group(function () {

    /* ------------------------------ Admin Routes ------------------------------ */
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Account Controller
        Route::get('/admin/account', [AccountController::class, 'index'])->name('admin.account');
        Route::get('/admin/account/create', [AccountController::class, 'create'])->name('admin.account.create');
        Route::post('/admin/account/store', [AccountController::class, 'store'])->name('admin.account.store');
        Route::get('/admin/account/edit/{user}', [AccountController::class, 'edit'])->name('admin.account.edit');
        Route::put('/admin/account/update/{user}', [AccountController::class, 'update'])->name('admin.account.update');
        Route::delete('admin/account/destroy/{user}', [AccountController::class, 'destroy'])
            ->name('admin.account.destroy');

        // Branch Controller
        Route::get('/admin/branch', [BranchController::class, 'index'])->name('admin.branch');
        Route::get('/admin/branch/create', [BranchController::class, 'create'])->name('admin.branch.create');
        Route::post('/admin/branch/store', [BranchController::class, 'store'])->name('admin.branch.store');
        Route::delete('/admin/branch/destroy/{branch}', [BranchController::class, 'destroy'])->name('admin.branch.destroy');

        // Destination Controller
        Route::get('/admin/destination', [LocationController::class, 'index'])->name('admin.destination');
        Route::post('/admin/destination/store', [LocationController::class, 'store'])->name('admin.destination.store');
        Route::delete('/admin/destination/destroy/{location}', [LocationController::class, 'destroy'])->name('admin.destination.destroy');

        // Container Controller
        Route::get('/container/list', [ContainerController::class, 'index'])->name('container.list');
        Route::post('/container/store', [ContainerController::class, 'store'])->name('container.store');
        Route::put('/container/update/{containerID}', [ContainerController::class, 'update'])->name('contianer.update');
        Route::delete('/container/delete/{container}', [ContainerController::class, 'destroy'])->name('container.delete');

        // Tracking Status Controller
        Route::get('/track/status', [TrackingStatusController::class, 'index'])->name('track.status');
        Route::post('/tracking-status/update', [TrackingStatusController::class, 'updateStatus'])
            ->name('tracking-status.update');

        // Receive Order Controller
        Route::get('/receive-order/list', [ReceiveOrderController::class, 'index'])->name('receive.order');

        // Container List Controller
        Route::get('/container/view', [ContainerListController::class, 'index'])->name('container.view');
        Route::get('/containers/export/{id}', [ContainerListController::class, 'export'])
            ->name('containers.export');
    });


    /* --------------------------- Branch Admin Routes -------------------------- */
    Route::middleware('role:branch_admin')->group(function () {
        Route::get('/branch/dashboard', [BranchDashboardController::class, 'index'])->name('branch.dashboard');

        // Branch Account Controller
        Route::get('/branch/account/list', [BranchAccountController::class, 'index'])->name('branch.account');
        Route::get('/branch/account/create', [BranchAccountController::class, 'create'])->name('branch.account.create');
        Route::post('/branch/account/store', [BranchAccountController::class, 'store'])->name('branch.account.store');
        Route::get('/branch/account/edit/{user}', [BranchAccountController::class, 'edit'])->name('branch.account.edit');
        Route::put('/branch/account/update/{user}', [BranchAccountController::class, 'update'])->name('branch.account.update');
        Route::delete('/branch/account/destroy/{user}', [BranchAccountController::class, 'destroy'])->name('branch.account.delete');

        // Collected Order Controller
        Route::get('/collect/orders', [CollectedOrderController::class, 'index'])->name('order.collected');
        Route::post('/collected-orders/collect', [CollectedOrderController::class, 'collect'])
            ->name('collected-orders.collect');

        // Load Container Controller
        Route::get('/container/load', [LoadContainerController::class, 'index'])->name('load.container');
        Route::post('/loaded-orders/container', [LoadContainerController::class, 'load'])->name('loaded-orders.container');
    });


    /* ---------------- Shared Routes for Admin and Branch Admin ---------------- */
    Route::middleware(['role:admin,branch_admin'])->group(function () {

        // Box Controller
        Route::get('/box/list', [BoxController::class, 'index'])->name('box.list');
        Route::get('/box/create', [BoxController::class, 'create'])->name('box.create');
        Route::post('/box/store', [BoxController::class, 'store'])->name('box.store');
        Route::delete('/box/delete/{box}', [BoxController::class, 'destroy'])->name('box.delete');

        // Order Controller
        Route::get('/order/list', [OrderController::class, 'index'])->name('order.list');
        Route::get('/order/create', [OrderController::class, 'create'])->name('order.create');
        Route::post('/order/store', [OrderController::class, 'store'])->name('order.store');
    });

    /* ----------------------- Authenticated Users Logout ----------------------- */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /* ------------------------------ Force Logout ------------------------------ */
    Route::get('/force-logout', function (Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Session::flush();

        return redirect('/');
    });
});
