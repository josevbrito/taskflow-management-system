<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

// Rotas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/2fa/verify', [AuthController::class, 'verify2FA']);

// Rotas protegidas por autenticação
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'getAuthenticatedUser']);

    // 2FA
    Route::post('/user/2fa/enable', [AuthController::class, 'enable2FA']);
    Route::post('/user/2fa/disable', [AuthController::class, 'disable2FA']);

    // Rotas de Recursos para Projects
    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->middleware('can:viewAny,' . Project::class);
        Route::post('/', [ProjectController::class, 'store'])->middleware('can:create,' . Project::class);
        Route::get('/{project}', [ProjectController::class, 'show'])->middleware('can:view,project');
        Route::put('/{project}', [ProjectController::class, 'update'])->middleware('can:update,project');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->middleware('can:delete,project');

        // Rotas de Membros do Projeto
        Route::get('/{project}/members', [ProjectController::class, 'getMembers'])->middleware('can:manageMembers,project');
        Route::post('/{project}/members', [ProjectController::class, 'addMember'])->middleware('can:manageMembers,project');
        Route::delete('/{project}/members/{user}', [ProjectController::class, 'removeMember'])->middleware('can:manageMembers,project');
    });

    // Rotas de Recursos para Tasks
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->middleware('can:viewAny,' . Task::class);
        Route::post('/', [TaskController::class, 'store'])->middleware('can:create,' . Task::class);
        Route::get('/{task}', [TaskController::class, 'show'])->middleware('can:view,task');
        Route::put('/{task}', [TaskController::class, 'update'])->middleware('can:update,task');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->middleware('can:delete,task');
    });

    // Rotas para Users
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('can:viewAny,' . User::class);
        Route::get('/{user}', [UserController::class, 'show'])->middleware('can:view,user');
        Route::post('/', [UserController::class, 'store'])->middleware('can:create,' . User::class);
        Route::put('/{user}', [UserController::class, 'update'])->middleware('can:update,user');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('can:delete,user');
    });

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

});
