<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskCommentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Retorna uma lista de comentários para uma tarefa específica.
     */
    public function index(Task $task)
    {
        $this->authorize('view', $task);

        return response()->json($task->comments()->with('user')->orderBy('created_at', 'asc')->get());
    }

    /**
     * Cria um novo comentário para uma tarefa específica.
     */
    public function store(Request $request, Task $task)
    {
        $this->authorize('createComment', $task);

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = $task->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return response()->json($comment->load('user'), 201);
    }

    /**
     * Retorna detalhes de um comentário específico de uma tarefa.
     */
    public function show(Task $task, TaskComment $comment)
    {
        if ($comment->task_id !== $task->id) {
            return response()->json(['message' => 'Comentário não pertence a esta tarefa.'], 404);
        }
        $this->authorize('view', $comment);

        return response()->json($comment->load('user'));
    }

    /**
     * Atualiza um comentário específico de uma tarefa.
     */
    public function update(Request $request, Task $task, TaskComment $comment)
    {
        if ($comment->task_id !== $task->id) {
            return response()->json(['message' => 'Comentário não pertence a esta tarefa.'], 404);
        }
        $this->authorize('update', $comment);

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update($request->all());

        return response()->json($comment->load('user'));
    }

    /**
     * Exclui um comentário específico de uma tarefa.
     */
    public function destroy(Task $task, TaskComment $comment)
    {
        if ($comment->task_id !== $task->id) {
            return response()->json(['message' => 'Comentário não pertence a esta tarefa.'], 404);
        }
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(null, 204);
    }
}
