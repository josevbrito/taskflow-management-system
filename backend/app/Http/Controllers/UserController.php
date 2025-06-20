<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

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
        $users = User::all();
        return response()->json($users);
    }

    /**
     * Mostra o perfil de um usuário específico.
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

}