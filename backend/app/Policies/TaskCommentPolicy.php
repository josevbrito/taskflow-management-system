<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TaskComment;
use App\Models\Task;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskCommentPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): Response|null
    {
        if ($user->isAdmin()) {
            return Response::allow();
        }
        return null;
    }

    /**
     * Determine whether the user can view the comment.
     * Quem pode ver a tarefa, pode ver o comentário.
     */
    public function view(User $user, TaskComment $comment): Response|bool
    {
        // Se o usuário pode ver a tarefa associada, pode ver o comentário.
        return $user->can('view', $comment->task)
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para visualizar este comentário.');
    }

    /**
     * Determine whether the user can create comments on a given task.
     */
    public function createComment(User $user, Task $task): Response|bool
    {
        return $user->can('view', $task)
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para adicionar comentários a esta tarefa.');
    }

    /**
     * Determine whether the user can update the comment.
     * Apenas o criador do comentário ou um admin/manager.
     */
    public function update(User $user, TaskComment $comment): Response|bool
    {
        return $user->isManager() || $user->id === $comment->user_id
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para atualizar este comentário.');
    }

    /**
     * Determine whether the user can delete the comment.
     * Apenas o criador do comentário ou um admin/manager.
     */
    public function delete(User $user, TaskComment $comment): Response|bool
    {
        return $user->isManager() || $user->id === $comment->user_id
                    ? Response::allow()
                    : Response::deny('Você não tem permissão para excluir este comentário.');
    }
}
