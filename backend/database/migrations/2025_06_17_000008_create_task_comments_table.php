<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Comentário pode ser resposta a outro comentário
            $table->foreignId('parent_comment_id')->nullable()->constrained('task_comments')->onDelete('cascade');
            
            // Conteúdo
            $table->text('content');
            $table->json('attachments')->nullable(); // Anexos do comentário
            
            // Status
            $table->boolean('is_internal')->default(false); // Comentário interno (não visível para cliente)
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            
            // Menções
            $table->json('mentions')->nullable(); // IDs dos usuários mencionados
            
            $table->timestamps();
            
            // Índices
            $table->index(['task_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('parent_comment_id');
            $table->index('is_internal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_comments');
    }
};