<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ManagerProject;

Route::get('/', function () {
    return redirect('login');
})->name('welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('projects')->group(function () {
        Route::get('/', [ProyectoController::class, 'index'])->name('project-index');
        Route::get('/create-project', [ProyectoController::class, 'create'])->name('create-project');
        
        Route::get('/l/{regionId}/{countryId}', [ProyectoController::class, 'projects'])->name('projects');
        Route::get('/s/{regionId}/{countryId}/{projectId}', [ProyectoController::class, 'project'])->name('project');
        
        Route::post('/store-project', [ProyectoController::class, 'store'])->name('store-project');

        /* AXIOS */
        Route::get('/navigate', [ProyectoController::class, 'navigate'])->name('navigate');
        Route::post('/navigate/addFile', [ProyectoController::class, 'navigateAddFile'])->name('navigate-add-file');
        Route::post('/navigate/addDir', [ProyectoController::class, 'navigateAddDir'])->name('navigate-add-dir');
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users');
        Route::get('/create-user', [UserController::class, 'create'])->name('create-user');

        /* STORE */
        Route::post('/store-user', [UserController::class, 'store'])->name('store-user');
    });

    Route::prefix('regions')->group(function(){
        Route::get('/', [RegionController::class, 'index'])->name('regions');
        Route::get('/create-region', [RegionController::class, 'create'])->name('create-region');

        Route::post('/store-region', [RegionController::class, 'store'])->name('store-region');
    });


    Route::prefix('manager')->group(function () {
        Route::get('/projects', [ManagerProject::class, 'index'])->name('manager-list');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
