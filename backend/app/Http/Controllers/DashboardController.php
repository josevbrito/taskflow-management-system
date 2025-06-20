<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    /**
     * Retorna estatísticas abrangentes para o dashboard.
     */
    public function stats(Request $request)
    {
        $user = Auth::user();

        // 1. Estatísticas Gerais
        $totalProjects = $user->projects()->count();
        $completedProjects = $user->projects()->where('status', 'Concluído')->count();
        $totalTasks = $user->assignedTasks()->count();
        $completedTasks = $user->assignedTasks()->where('status', 'Concluída')->count();
        $pendingTasks = $user->assignedTasks()->where('status', 'Pendente')->count();
        $inProgressTasks = $user->assignedTasks()->where('status', 'Em Progresso')->count();
        $overdueTasks = Task::where('due_date', '<', now())
                            ->whereIn('status', ['Pendente', 'Em Progresso']);

        if ($user->isUser()) {
            $overdueTasks->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('created_by', $user->id);
            });
        }
        $overdueTasks = $overdueTasks->count();


        // 2. Distribuição de Projetos por Status
        $projectStatusQuery = Project::query();
        if ($user->isUser()) {
            $projectStatusQuery->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('members', function ($memberQuery) use ($user) {
                      $memberQuery->where('user_id', $user->id);
                  });
            });
        }
        $projectStatusBreakdown = $projectStatusQuery->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();


        // 3. Distribuição de Tarefas por Status
        $taskStatusQuery = Task::query();
        if ($user->isUser()) {
            $taskStatusQuery->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('created_by', $user->id)
                  ->orWhereHas('project', function ($projectQuery) use ($user) {
                      $projectQuery->where('user_id', $user->id)
                                   ->orWhereHas('members', function ($memberQuery) use ($user) {
                                       $memberQuery->where('user_id', $user->id);
                                   });
                  });
            });
        }
        $taskStatusBreakdown = $taskStatusQuery->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();


        // 4. Distribuição de Tarefas por Prioridade
        $taskPriorityQuery = Task::query();
        if ($user->isUser()) {
            $taskPriorityQuery->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('created_by', $user->id)
                  ->orWhereHas('project', function ($projectQuery) use ($user) {
                      $projectQuery->where('user_id', $user->id)
                                   ->orWhereHas('members', function ($memberQuery) use ($user) {
                                       $memberQuery->where('user_id', $user->id);
                                   });
                  });
            });
        }
        $taskPriorityBreakdown = $taskPriorityQuery->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get();


        // 5. Ranking de Usuários por Tarefas Atribuídas
        $userTaskRankingQuery = User::select('users.id', 'users.name', DB::raw('count(tasks.id) as tasks_count'))
            ->leftJoin('tasks', 'users.id', '=', 'tasks.assigned_to')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('tasks_count')
            ->limit(5);

        if ($user->isUser()) {
            // Para usuários comuns, mostrar apenas o seu próprio ranking
            $userTaskRankingQuery->where('users.id', $user->id);
        }
        $userTaskRanking = $userTaskRankingQuery->get();

        // 6. Últimas Tarefas Ativas (para "Atividades Recentes")
        $recentTasksQuery = Task::whereIn('status', ['Pendente', 'Em Progresso'])
            ->orderBy('updated_at', 'desc')
            ->limit(5);

        if ($user->isUser()) {
            $recentTasksQuery->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('created_by', $user->id)
                  ->orWhereHas('project', function ($projectQuery) use ($user) {
                      $projectQuery->where('user_id', $user->id)
                                   ->orWhereHas('members', function ($memberQuery) use ($user) {
                                       $memberQuery->where('user_id', $user->id);
                                   });
                  });
            });
        }
        $recentTasks = $recentTasksQuery->with('project', 'assignedUser')->get();

        return response()->json([
            'general_stats' => [
                'total_projects' => $totalProjects,
                'completed_projects' => $completedProjects,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'pending_tasks' => $pendingTasks,
                'in_progress_tasks' => $inProgressTasks,
                'overdue_tasks' => $overdueTasks,
                'progress_percentage_tasks' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0, // Percentagem de conclusão
            ],
            'project_status_breakdown' => $projectStatusBreakdown,
            'task_status_breakdown' => $taskStatusBreakdown,
            'task_priority_breakdown' => $taskPriorityBreakdown,
            'user_task_ranking' => $userTaskRanking,
            'recent_tasks' => $recentTasks,
        ]);
    }
}
