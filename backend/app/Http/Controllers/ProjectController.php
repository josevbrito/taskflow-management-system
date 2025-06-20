<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
    use AuthorizesRequests;


    /**
     * Retorna uma lista de projetos paginada e com filtro de pesquisa.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Project::class);

        $user = Auth::user();
        $query = Project::query();

        // Aplica filtro de pesquisa
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
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

        // Se 'all' for true, retorna todos os usuários(para dropdowns de tarefas, etc.)
        if ($request->has('all') && $request->get('all') == 'true') {
            return response()->json($query->with('user', 'members.user')->get());
        }

        // Aplica filtro de usuário (se não for admin/manager)
        if ($user->isUser()) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('members', function ($memberQuery) use ($user) {
                      $memberQuery->where('user_id', $user->id);
                  });
            });
        }

        $projects = $query->with('user', 'members.user')
                          ->orderBy('created_at', 'desc')
                          ->paginate(5);

        return response()->json($projects);
    }

    /**
     * Cria um novo projeto.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

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
        if (!$project->members()->where('user_id', Auth::id())->exists()) {
            $project->members()->create([
                'user_id' => Auth::id(),
                'role' => 'owner',
            ]);
        }

        return response()->json($project->load('user', 'members.user'), 201);
    }

    /**
     * Retorna detalhes de um projeto específico.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return response()->json($project->load('user', 'tasks', 'members.user'));
    }

    /**
     * Atualiza um projeto existente.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

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
        $this->authorize('delete', $project);

        $project->tasks()->delete();
        $project->members()->delete();

        $project->delete();

        return response()->json(null, 204);
    }

    /**
     * Retorna a lista de membros de um projeto específico.
     */
    public function getMembers(Project $project)
    {
        $this->authorize('manageMembers', $project);

        // Carrega os usuáriosassociados aos membros
        return response()->json($project->members()->with('user')->get());
    }

    /**
     * Adiciona um usuário como membro a um projeto.
     */
    public function addMember(Request $request, Project $project)
    {
        $this->authorize('manageMembers', $project);

        $request->validate([
            'user_id' => ['required', 'exists:users,id',
                Rule::unique('project_members')->where(function ($query) use ($project) {
                    return $query->where('project_id', $project->id);
                })
            ],
            'role' => ['nullable', Rule::in(['member', 'viewer', 'contributor', 'owner'])],
        ]);

        $member = $project->members()->create([
            'user_id' => $request->user_id,
            'role' => $request->input('role', 'member'),
        ]);

        return response()->json($member->load('user'), 201);
    }

    /**
     * Remove um usuário como membro de um projeto.
     */
    public function removeMember(Project $project, User $user)
    {
        $this->authorize('manageMembers', $project);

        if ($project->user_id === $user->id) {
            return response()->json(['message' => 'Não é possível remover o criador original do projeto.'], 403);
        }

        $project->members()->where('user_id', $user->id)->delete();

        return response()->json(null, 204);
    }
}
