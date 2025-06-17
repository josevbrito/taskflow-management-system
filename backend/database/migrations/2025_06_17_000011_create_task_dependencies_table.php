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
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade'); // Tarefa dependente
            $table->foreignId('depends_on_task_id')->constrained('tasks')->onDelete('cascade'); // Tarefa da qual depende
            
            // Tipo de dependência
            $table->enum('dependency_type', [
                'finish_to_start', // Tarefa B só pode começar quando A terminar
                'start_to_start',  // Tarefa B só pode começar quando A começar
                'finish_to_finish', // Tarefa B só pode terminar quando A terminar
                'start_to_finish'   // Tarefa B só pode terminar quando A começar
            ])->default('finish_to_start');
            
            // Lag time (tempo de espera em dias)
            $table->integer('lag_days')->default(0);
            
            // Quem criou a dependência
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
            
            // Chave única para evitar dependências duplicadas
            $table->unique(['task_id', 'depends_on_task_id']);
            
            // Índices
            $table->index(['task_id', 'dependency_type']);
            $table->index('depends_on_task_id');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_dependencies');
    }
};