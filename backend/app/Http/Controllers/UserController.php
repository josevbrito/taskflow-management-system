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
        $users = User::select('id', 'name', 'email', 'role')->get();

        return response()->json($users);
    }

    public function show(User $user)
    {

        return response()->json($user);
    }

}