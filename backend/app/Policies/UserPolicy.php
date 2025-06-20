<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     * @param  \App\Models\User  $user O usuário autenticado
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user): Response|bool
    {
        return $user->isAdmin()
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para listar usuários.');
    }

    /**
     * Determine whether the user can view the model.
     * @param  \App\Models\User  $user O usuário autenticado
     * @param  \App\Models\User  $model O usuário que está sendo visualizado
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $model): Response|bool
    {
        // Um usuário pode ver seu próprio perfil, ou um administrador pode ver qualquer perfil
        return ($user->isAdmin() || $user->id === $model->id)
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para visualizar este perfil.');
    }

    /**
     * Determine whether the user can create models.
     * @param  \App\Models\User  $user O usuário autenticado
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user): Response|bool
    {
        // Apenas administradores podem criar novos usuários (excluindo a rota de registro público)
        return $user->isAdmin()
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para criar usuários.');
    }

    /**
     * Determine whether the user can update the model.
     * @param  \App\Models\User  $user O usuário autenticado
     * @param  \App\Models\User  $model O usuário que está sendo atualizado
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $model): Response|bool
    {
        // Um usuário pode atualizar seu próprio perfil, ou um administrador pode atualizar qualquer perfil
        return ($user->isAdmin() || $user->id === $model->id)
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para atualizar este perfil.');
    }

    /**
     * Determine whether the user can delete the model.
     * @param  \App\Models\User  $user O usuário autenticado
     * @param  \App\Models\User  $model O usuário que está sendo excluído
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $model): Response|bool
    {
        // Apenas administradores podem excluir usuários, e um usuário não pode excluir a si mesmo
        return ($user->isAdmin() && $user->id !== $model->id)
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para excluir este usuário ou não pode excluir a si mesmo.');
    }
}

