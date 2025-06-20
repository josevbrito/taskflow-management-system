<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
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
     * Ver todas as tarefas que lhe foram atribuídas ou que ele criou, ou todas se for manager/admin.
     */
    public function viewAny(User $user): Response|bool
    {
        // Managers podem ver todas as tarefas.
        // Usuários podem ver tarefas que lhes foram atribuídas ou que criaram.
        return $user->isManager() || $user->isUser()
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para listar tarefas.');
    }

    /**
     * Determine whether the user can view the task.
     */
    public function view(User $user, Task $task): Response|bool
    {
        // Managers podem ver qualquer tarefa.
        // Usuários podem ver tarefas que lhes foram atribuídas ou que criaram, ou tarefas em projetos que eles criaram/são membros.
        return $user->id === $task->assigned_to || $user->id === $task->created_by ||
               $user->id === $task->project->user_id || $task->project->members->contains('user_id', $user->id)
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para visualizar esta tarefa.');
    }

    /**
     * Determine whether the user can create tasks.
     * Administradores, gestores e usuários podem criar tarefas.
     */
    public function create(User $user): Response|bool
    {
        // Todos os roles podem criar tarefas, desde que o projeto seja acessível.
        return $user->isManager() || $user->isUser()
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para criar tarefas.');
    }

    /**
     * Determine whether the user can update the task.
     * Administradores e gestores podem atualizar qualquer tarefa.
     * Usuários podem atualizar tarefas que lhes foram atribuídas ou que criaram.
     */
    public function update(User $user, Task $task): Response|bool
    {
        // Managers podem atualizar qualquer tarefa.
        // Usuários podem atualizar tarefas que lhes foram atribuídas ou que criaram.
        return $user->isManager() || $user->id === $task->assigned_to || $user->id === $task->created_by
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para atualizar esta tarefa.');
    }

    /**
     * Determine whether the user can delete the task.
     * Administradores e gestores podem excluir qualquer tarefa.
     * Usuários só podem excluir tarefas que criaram.
     */
    public function delete(User $user, Task $task): Response|bool
    {
        // Managers podem excluir qualquer tarefa.
        // Usuários podem excluir tarefas que criaram.
        return $user->isManager() || $user->id === $task->created_by
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para excluir esta tarefa.');
    }
}
