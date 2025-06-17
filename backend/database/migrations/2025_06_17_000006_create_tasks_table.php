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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            
            // Relacionamentos
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('parent_task_id')->nullable()->constrained('tasks')->onDelete('cascade'); // Para subtarefas
            
            // Status e prioridade
            $table->enum('status', ['todo', 'in_progress', 'review', 'done', 'cancelled'])->default('todo');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            
            // Datas
            $table->datetime('start_date')->nullable();
            $table->datetime('due_date')->nullable();
            $table->datetime('completed_at')->nullable();
            
            // Estimativas e tempo
            $table->integer('estimated_hours')->nullable(); // Tempo estimado em horas
            $table->integer('actual_hours')->nullable(); // Tempo real gasto em horas
            $table->integer('progress_percentage')->default(0);
            
            // Configurações visuais
            $table->string('color', 7)->nullable(); // Cor hex
            $table->json('tags')->nullable(); // Array de tags
            
            // Anexos e links
            $table->json('attachments')->nullable(); // Array de caminhos de arquivos
            $table->json('links')->nullable(); // Array de links externos
            
            // Checklist
            $table->json('checklist')->nullable(); // Array de itens do checklist
            
            // Campos para recurring tasks
            $table->boolean('is_recurring')->default(false);
            $table->json('recurring_config')->nullable(); // Configuração de recorrência
            $table->foreignId('recurring_parent_id')->nullable()->constrained('tasks')->onDelete('cascade');
            
            // Campos para tracking
            $table->boolean('is_billable')->default(false);
            $table->decimal('hourly_rate', 8, 2)->nullable();
            
            // Ordem/Posição (para drag & drop)
            $table->integer('sort_order')->default(0);
            
            // Metadados
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Índices para performance
            $table->index(['project_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['created_by', 'status']);
            $table->index(['due_date', 'status']);
            $table->index(['priority', 'status']);
            $table->index('parent_task_id');
            $table->index('sort_order');
            $table->index('is_recurring');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};