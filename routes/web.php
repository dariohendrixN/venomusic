<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\RoleApprovalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})
->middleware(['auth', 'verified'])
->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
    ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
    ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
    ->name('profile.destroy');
});

Route::get('/admin', [AdminController::class, 'dashboard'])
->middleware(['auth', 'role:admin'])
->name('admin.dashboard');


Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/role-requests', [RoleApprovalController::class, 'index'])
    ->name('admin.role-requests');
    Route::post('/role-requests/{user}/{role}/approve', [RoleApprovalController::class, 'approve'])
    ->name('admin.role-requests.approve');
    Route::post('/role-requests/{user}/{role}/reject', [RoleApprovalController::class, 'reject'])
    ->name('admin.role-requests.reject');
});

Route::post('/roles/request', [RoleRequestController::class, 'requestRole'])
->middleware(['auth'])
->name('roles.request');

require __DIR__.'/auth.php';
