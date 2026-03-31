<?php

use App\Http\Controllers\Admin\RoleApprovalController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DiscoverController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileGenreController;
use App\Http\Controllers\ProfileImageController;
use App\Http\Controllers\RoleRequestController;
use App\Http\Controllers\TrackController;
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

Route::get('/genres/search', [GenreController::class, 'search'])
    ->name('genres.search');

Route::post('/profile/genres', [ProfileGenreController::class, 'store'])
    ->middleware('auth')
    ->name('profile.genres.store');    
    Route::delete('/profile/genres/{genre}', [ProfileGenreController::class, 'destroy'])
        ->middleware('auth')
        ->name('profile.genres.destroy');

Route::get('/discover', [DiscoverController::class, 'index'])
    ->name('discover');

Route::get('/discover/search', [DiscoverController::class, 'search'])
    ->name('discover.search');

Route::post('/profile/images', [ProfileImageController::class, 'store'])
    ->middleware('auth')
    ->name('profile.images.store');
    Route::delete('/profile/images/{image}', [ProfileImageController::class, 'destroy'])
        ->middleware('auth')
        ->name('profile.images.destroy');

Route::post('/profile/tracks', [TrackController::class, 'store'])
    ->middleware('auth')
    ->name('profile.tracks.store');
    Route::delete('/profile/tracks/{track}', [TrackController::class, 'destroy'])
        ->middleware('auth')
        ->name('profile.tracks.destroy');

Route::patch('/profile/links', [ProfileController::class, 'updateLinks'])
    ->middleware('auth')
    ->name('profile.links.update');
    Route::delete('/profile/links/{link}', [ProfileController::class, 'destroyLink'])
        ->middleware('auth')
        ->name('profile.links.destroy');



// Route::get('/discover/{genre}', [DiscoverController::class, 'search'])
//     ->name('discover.search');


require __DIR__.'/auth.php';
