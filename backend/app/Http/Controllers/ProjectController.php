<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function __construct()
    {
        // Aplica a ProjectPolicy para autorizar automaticamente os métodos CRUD
        $this->authorizeResource(Project::class, 'project');
    }

    /**
     * Retorna uma lista de projetos.
     */
    public function index()
    {
        $user = Auth::user();
        // Verifica o papel do usuário e retorna os projetos adequados
        if ($user->isUser()) {
            $projects = Project::where('user_id', $user->id)
                            ->orWhereHas('members', function ($query) use ($user) {
                                $query->where('user_id', $user->id);
                            })
                            ->with('user', 'members.user')
                            ->get();
        } else {
            // Admin e Manager podem ver todos os projetos
            $projects = Project::with('user', 'members.user')->get();
        }

        return response()->json($projects);
    }

    /**
     * Cria um novo projeto.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['nullable', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
        ]);

        $project = Auth::user()->projects()->create($request->all());

        // Adiciona o criador como membro do projeto por padrão
        $project->members()->create([
            'user_id' => Auth::id(),
            'role' => 'owner',
        ]);

        return response()->json($project->load('user', 'members.user'), 201);
    }

    /**
     * Retorna detalhes de um projeto específico.
     */
    public function show(Project $project)
    {
        return response()->json($project->load('user', 'tasks', 'members.user'));
    }

    /**
     * Atualiza um projeto existente.
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['nullable', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
        ]);

        $project->update($request->all());

        return response()->json($project->load('user', 'members.user'));
    }

    /**
     * Exclui um projeto.
     */
    public function destroy(Project $project)
    {
        $project->tasks()->delete();
        $project->members()->delete();

        $project->delete();

        return response()->json(null, 204);
    }
}