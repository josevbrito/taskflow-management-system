<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Retorna uma lista paginada de usuários com filtro de pesquisa.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        // Aplicar filtro de pesquisa (por nome ou email)
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }
        
        if ($request->has('all') && $request->all == 'true') {
            return response()->json($query->select('id', 'name', 'email', 'role', 'avatar')->get());
        }

        $users = $query->orderBy('name', 'asc')
                       ->paginate(5);

        return response()->json($users);
    }

    /**
     * Retorna o perfil de um usuário específico.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        return response()->json($user);
    }

    /**
     * Cria um novo usuário.
     */
    public function store(UserStoreRequest $request)
    {
        $this->authorize('create', User::class);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return response()->json($user, 201);
    }

    /**
     * Atualiza um usuário existente.
     */
    public function update(UserUpdateRequest $request, User $userModel)
    {
        $this->authorize('update', $userModel);

        $userModel->name = $request->name;
        $userModel->email = $request->email;
        $userModel->role = $request->role;
        $userModel->is_active = $request->input('is_active', $userModel->is_active);

        if ($request->filled('password')) {
            $userModel->password = Hash::make($request->password);
        }

        $userModel->save();

        return response()->json($userModel);
    }

    /**
     * Exclui um usuário.
     */
    public function destroy(User $userModel)
    {
        $this->authorize('delete', $userModel);

        $userModel->delete();

        return response()->json(null, 204);
    }
}
