<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function __construct()
    {
        // Aplica a TaskPolicy para autorizar automaticamente os métodos CRUD
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Retorna uma lista de tarefas do usuário autenticado ou de projetos que ele participa.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isManager()) {
            $tasks = Task::with('project', 'assignedUser', 'createdBy')->get();
        } else {
            $tasks = Task::where('assigned_to', $user->id)
                         ->orWhere('created_by', $user->id)
                         ->orWhereHas('project', function ($query) use ($user) {
                             $query->where('user_id', $user->id)
                                   ->orWhereHas('members', function ($memberQuery) use ($user) {
                                       $memberQuery->where('user_id', $user->id);
                                   });
                         })
                         ->with('project', 'assignedUser', 'createdBy')
                         ->get();
        }

        return response()->json($tasks);
    }

    /**
     * Cria uma nova tarefa.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'status' => ['nullable', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
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
        return response()->json($task->load('project', 'assignedUser', 'createdBy', 'comments.user', 'timeLogs.user', 'dependencies', 'dependentTasks', 'fileUploads'));
    }

    /**
     * Atualiza uma tarefa existente.
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'status' => ['nullable', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'due_date' => 'nullable|date',
        ]);

        // Validação adicional de acesso ao projeto
        $project = Project::find($request->project_id);
        if (!$project || (!Auth::user()->isAdmin() && !Auth::user()->isManager() &&
            $project->user_id !== Auth::id() && !$project->members->contains('user_id', Auth::id()))) {
            return response()->json(['message' => 'Unauthorized: You do not have access to the target project.'], 403);
        }

        $task->update($request->all());

        return response()->json($task->load('project', 'assignedUser', 'createdBy'));
    }

    /**
     * Exclui uma tarefa.
     */
    public function destroy(Task $task)
    {
        $task->comments()->delete();
        $task->timeLogs()->delete();
        $task->dependencies()->detach();
        $task->dependentTasks()->detach();

        $task->delete();

        return response()->json(null, 204);
    }
}