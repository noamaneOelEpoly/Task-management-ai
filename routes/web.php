<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminTaskController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminUserProfileController;
use App\Http\Controllers\AdminAIController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/tasks');
});

// Regular authenticated users - manage ONLY their own data
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard redirects to tasks
    Route::get('/dashboard', function () {
        return redirect('/tasks');
    })->name('dashboard');

    // AI Assistant UI
    Route::get('/ai', [AdminAIController::class, 'index'])->name('ai.assistant');

    // AI Assistant (all authenticated users)
    Route::post('/ai/query', [AdminAIController::class, 'query'])->name('ai.query');
    Route::get('/ai/analytics', [AdminAIController::class, 'analytics'])->name('ai.analytics');
    Route::post('/ai/search', [AdminAIController::class, 'search'])->name('ai.search');
    
    // Tasks Routes - user's own tasks
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::get('tasks/category/{category}', [TaskController::class, 'filterByCategory'])->name('tasks.filterByCategory');

    // Categories Routes - user's own categories
    Route::resource('categories', CategoryController::class);
    
    // Profile Routes - user's own profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes - manage ALL users and their data
Route::middleware(['auth', 'authAdmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users management
    Route::resource('users', AdminController::class);
    Route::get('/users/{user}/edit-profile', [AdminUserProfileController::class, 'editProfile'])->name('users.edit-profile');
    Route::patch('/users/{user}/update-profile', [AdminUserProfileController::class, 'updateProfile'])->name('users.update-profile');
    Route::get('/users/{user}/edit-password', [AdminUserProfileController::class, 'editPassword'])->name('users.edit-password');
    Route::patch('/users/{user}/update-password', [AdminUserProfileController::class, 'updatePassword'])->name('users.update-password');
    
    // Tasks management
    Route::resource('tasks', AdminTaskController::class);
    
    // Categories management
    Route::resource('categories', AdminCategoryController::class);
});

require __DIR__.'/auth.php';
