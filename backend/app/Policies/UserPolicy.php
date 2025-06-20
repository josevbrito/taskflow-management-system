<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário autenticado é um administrador.
     */
    public function before(User $user, string $ability): Response|null
    {
        if ($user->isAdmin()) {
            return Response::allow();
        }

        return null;
    }

    /**
     * Determina se o usuário autenticado pode ver uma lista de outros usuários (admins e managers).
     */
    public function viewAny(User $user): Response|bool
    {
        // Administradores e gestores podem ver uma lista de usuários.
        return $user->isManager() || $user->isUser()
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para listar usuários.');
    }

    /**
     * Determina se o usuário autenticado pode ver o perfil de um usuário específico.
     */
    public function view(User $user, User $model): Response|bool
    {
        return $user->id === $model->id || $user->isManager()
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para visualizar este perfil.');
    }

    /**
     * Determina se o usuário autenticado pode criar um novo usuário.
     * Apenas administradores.
     */
    public function create(User $user): Response|bool
    {
        return Response::deny('Você não tem permissão para criar usuários.');
    }

    /**
     * Determina se o usuário autenticado pode atualizar um perfil de usuário.
     */
    public function update(User $user, User $model): Response|bool
    {
        if ($user->id === $model->id) {
            return Response::allow();
        }

        if ($user->isManager() && !$model->isAdmin() && !$model->isManager()) {
            return Response::allow();
        }

        return Response::deny('Você não tem permissão para atualizar este perfil.');
    }

    /**
     * Determina se o usuário autenticado pode excluir um usuário.
     * Apenas administradores, e não pode excluir a si mesmo.
     */
    public function delete(User $user, User $model): Response|bool
    {
        if ($user->id === $model->id) {
            return Response::deny('Você não pode excluir a si mesmo.');
        }

        if ($user->isManager() && !$model->isAdmin() && !$model->isManager()) {
            return Response::allow();
        }

        return Response::deny('Você não tem permissão para excluir este usuário.');
    }
}
