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

    /**
     * Retorna uma lista de tarefas do usuário autenticado ou de projetos que ele participa.
     */
    public function index()
    {
        $user = Auth::user();

        // Tarefas atribuídas diretamente ao usuário
        $tasks = $user->assignedTasks()
                    ->with('project', 'assignedUser') 
                    ->get();

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
            'project_id' => 'required|exists:projects,id', // Garante que o projeto exista
            'assigned_to' => 'required|exists:users,id',   // Garante que o usuário exista
            'status' => ['nullable', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'due_date' => 'nullable|date',
        ]);

        // Garante que o usuário autenticado tem permissão para criar tarefa neste projeto
        $project = Project::find($request->project_id);
        if (!$project || ($project->user_id !== Auth::id() && !$project->members->contains('user_id', Auth::id()))) {
            return response()->json(['message' => 'Unauthorized to create task in this project.'], 403);
        }

        $task = Task::create($request->all());


        return response()->json($task->load('project', 'assignedUser'), 201);
    }

    /**
     * Retorna detalhes de uma tarefa específica.
     */
    public function show(Task $task)
    {
        // Garante que o usuário tem acesso à tarefa (criou, atribuído, ou membro do projeto)
        $user = Auth::user();
        if ($task->assigned_to !== $user->id && $task->project->user_id !== $user->id && !$task->project->members->contains('user_id', $user->id)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($task->load('project', 'assignedUser', 'comments.user', 'timeLogs.user', 'dependencies', 'fileUploads'));
    }

    /**
     * Atualiza uma tarefa existente.
     */
    public function update(Request $request, Task $task)
    {
        // Garante que o usuário tem permissão para atualizar
        $user = Auth::user();
        if ($task->assigned_to !== $user->id && $task->project->user_id !== $user->id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized to update this task.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'status' => ['nullable', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'due_date' => 'nullable|date',
        ]);

        $task->update($request->all());

        return response()->json($task->load('project', 'assignedUser'));
    }

    /**
     * Exclui uma tarefa.
     */
    public function destroy(Task $task)
    {
        // Garante que o usuário tem permissão para excluir
        $user = Auth::user();
        if ($task->project->user_id !== $user->id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized to delete this task.'], 403);
        }

        $taskTitle = $task->title;
        $taskId = $task->id;
        $projectName = $task->project->name;

        $task->comments()->delete();
        $task->timeLogs()->delete();
        $task->dependencies()->delete();

        $task->delete();

        return response()->json(null, 204);
    }
}