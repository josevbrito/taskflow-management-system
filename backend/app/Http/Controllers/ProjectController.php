<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Validation\Rule; 

class ProjectController extends Controller
{

    /**
     * Retorna uma lista de projetos do usuário autenticado.
     */
    public function index()
    {

        $projects = Auth::user()->projects() 
                        ->orWhereHas('members', function ($query) {
                            $query->where('user_id', Auth::id());
                        })
                        ->with('user', 'members.user') 
                        ->get();

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
        if ($project->user_id !== Auth::id() && !$project->members->contains('user_id', Auth::id())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($project->load('user', 'tasks', 'members.user'));
    }

    /**
     * Atualiza um projeto existente.
     */
    public function update(Request $request, Project $project)
    {
        if ($project->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized to update this project.'], 403);
        }

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
        // Apenas o criador ou um admin pode deletar
        if ($project->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized to delete this project.'], 403);
        }

        $projectName = $project->name;
        $projectId = $project->id;

        $project->tasks()->delete();
        $project->members()->delete();

        $project->delete();

        return response()->json(null, 204);
    }
}