<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    // Antes de qualquer método de autorização, permite administradores acessarem tudo
    public function before(User $user, string $ability): Response|null
    {
        if ($user->isAdmin()) {
            return Response::allow();
        }

        return null;
    }

    /**
     * Determine whether the user can view any projects.
     * (Ver todos os projetos que ele criou ou dos quais é membro, ou todos se for manager/admin).
     */
    public function viewAny(User $user): Response|bool
    {
        // Managers podem ver todos os projetos.
        // Usuários podem ver projetos que criaram ou dos quais são membros.
        return $user->isManager() || $user->isUser()
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para listar projetos.');
    }

    /**
     * Determine whether the user can view the project.
     */
    public function view(User $user, Project $project): Response|bool
    {
        // Managers podem ver qualquer projeto.
        // Usuários podem ver projetos que criaram ou dos quais são membros.
        return $user->isManager() || $user->id === $project->user_id || $project->members->contains('user_id', $user->id)
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para visualizar este projeto.');
    }

    /**
     * Determine whether the user can create projects.
     * Apenas administradores e gestores.
     */
    public function create(User $user): Response|bool
    {
        // Administradores e gestores podem criar projetos.
        return $user->isManager()
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para criar projetos.');
    }

    /**
     * Determine whether the user can update the project.
     * Administradores e gestores podem atualizar qualquer projeto.
     * Usuários só podem atualizar projetos que criaram.
     */
    public function update(User $user, Project $project): Response|bool
    {
        // Gestores podem atualizar qualquer projeto.
        // Usuários podem atualizar projetos que criaram.
        return $user->isManager() || $user->id === $project->user_id
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para atualizar este projeto.');
    }

    /**
     * Determine whether the user can delete the project.
     * Administradores e gestores podem excluir qualquer projeto.
     * Usuários só podem excluir projetos que criaram.
     */
    public function delete(User $user, Project $project): Response|bool
    {
        return $user->isManager() || $user->id === $project->user_id
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para excluir este projeto.');
    }

    /**
     * Permitido para o criador do projeto, admins e managers.
     */
    public function manageMembers(User $user, Project $project): Response|bool
    {
        return $user->isManager() || $user->id === $project->user_id
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para gerenciar membros deste projeto.');
    }
}
