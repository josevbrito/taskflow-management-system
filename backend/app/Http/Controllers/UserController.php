<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    /**
     * Retorna uma lista de todos os usuários.
     */
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'role', 'avatar')->get();
        return response()->json($users);
    }

    /**
     * Retorna uma lista de todos os usuários (APENAS para administradores).
     */
    public function adminIndex()
    {
        // Verifica se o usuário autenticado é um administrador
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden: You do not have administrator access.'], 403);
        }
        $users = User::all();
        return response()->json($users);
    }

    /**
     * Mostra o perfil de um usuário específico.
     */
    public function show(User $user)
    {
        // Permite que um usuário veja seu próprio perfil ou que um admin veja qualquer perfil
        if (Auth::id() !== $user->id && !Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized to view this user profile.'], 403);
        }
        return response()->json($user);
    }

}