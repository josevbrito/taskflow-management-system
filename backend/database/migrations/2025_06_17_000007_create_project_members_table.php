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
        Schema::create('project_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Papel do usuário no projeto
            $table->enum('role', ['owner', 'admin', 'member', 'viewer'])->default('member');
            
            // Permissões específicas
            $table->boolean('can_create_tasks')->default(true);
            $table->boolean('can_edit_tasks')->default(true);
            $table->boolean('can_delete_tasks')->default(false);
            $table->boolean('can_assign_tasks')->default(true);
            $table->boolean('can_view_reports')->default(true);
            $table->boolean('can_manage_members')->default(false);
            
            // Status do membro
            $table->boolean('is_active')->default(true);
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable();
            
            // Quem convidou/removeu
            $table->foreignId('invited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('removed_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Configurações de notificação
            $table->json('notification_settings')->nullable();
            
            $table->timestamps();
            
            // Chave única composta
            $table->unique(['project_id', 'user_id']);
            
            // Índices
            $table->index(['project_id', 'is_active']);
            $table->index(['user_id', 'is_active']);
            $table->index('role');
            $table->index('joined_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_members');
    }
};