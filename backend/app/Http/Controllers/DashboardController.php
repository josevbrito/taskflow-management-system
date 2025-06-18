<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{


    /**
     * Retorna estatísticas e atividades recentes para o dashboard do usuário autenticado.
     */
    public function stats(Request $request)
    {
        $user = Auth::user();

        $totalProjects = $user->projects()->count();
        $completedProjects = $user->projects()->where('status', 'completed')->count();
        $totalTasks = $user->assignedTasks()->count();
        $completedTasks = $user->assignedTasks()->where('status', 'completed')->count();
        $pendingTasks = $user->assignedTasks()->where('status', 'pending')->count();
        $inProgressTasks = $user->assignedTasks()->where('status', 'in_progress')->count();


        return response()->json([
            'total_projects' => $totalProjects,
            'completed_projects' => $completedProjects,
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks,
            'in_progress_tasks' => $inProgressTasks,
        ]);
    }
}