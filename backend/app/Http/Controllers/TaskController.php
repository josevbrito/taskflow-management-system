<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Retorna uma lista de tarefas paginada e com filtro de pesquisa e status/prioridade.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);
        $user = Auth::user();
        $query = Task::query();

        // Aplica filtro de pesquisa
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Aplica filtro de status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Aplica filtro de prioridade
        if ($request->has('priority') && !empty($request->priority)) {
            $query->where('priority', $request->priority);
        }

        // Aplica filtro de usuário (se não for admin/manager)
        if ($user->isUser()) {
            $query->where(function ($q) use ($user) {
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

        // Retorna os resultados paginados (5 itens por página) e carrega os relacionamentos
        $tasks = $query->with('project', 'assignedUser', 'createdBy')
                       ->orderBy('created_at', 'desc')
                       ->paginate(5);

        return response()->json($tasks);
    }

    /**
     * Cria uma nova tarefa.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Task::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'status' => ['nullable', Rule::in(['Pendente', 'Em Progresso', 'Concluída', 'Cancelada'])],
            'priority' => ['nullable', Rule::in(['Baixa', 'Média', 'Alta'])],
            'due_date' => 'nullable|date',
        ]);

        // Garante que o usuário autenticado tem permissão para criar tarefa neste projeto
        $project = Project::find($request->project_id);
        if (!$project || (!Auth::user()->isAdmin() && !Auth::user()->isManager() &&
            $project->user_id !== Auth::id() && !$project->members->contains('user_id', Auth::id()))) {
            return response()->json(['message' => 'Unauthorized: You do not have access to this project.'], 403);
        }

        $taskData = $request->all();
        $taskData['created_by'] = Auth::id();

        $task = Task::create($taskData);

        return response()->json($task->load('project', 'assignedUser', 'createdBy'), 201);
    }

    /**
     * Retorna detalhes de uma tarefa específica.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return response()->json($task->load('project', 'assignedUser', 'createdBy', 'comments.user'));
    }

    /**
     * Atualiza uma tarefa existente.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'status' => ['nullable', Rule::in(['Pendente', 'Em Progresso', 'Concluída', 'Cancelada'])],
            'priority' => ['nullable', Rule::in(['Baixa', 'Média', 'Alta'])],
            'due_date' => 'nullable|date',
        ]);

        // Validação adicional de acesso ao projeto
        $project = Project::find($request->project_id);
        if (!$project || (!Auth::user()->isAdmin() && !Auth::user()->isManager() &&
            $project->user_id !== Auth::id() && !$project->members->contains('user_id', Auth::id()))) {
            return response()->json(['message' => 'Unauthorized: You do not have access to the target project.'], 403);
        }

        $data = $request->except('created_by');
        $task->update($data);

        return response()->json($task->load('project', 'assignedUser', 'createdBy'));
    }

    /**
     * Exclui uma tarefa.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->comments()->delete();

        $task->delete();

        return response()->json(null, 204);
    }
}
