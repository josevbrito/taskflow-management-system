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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            // Relacionamento com usuário
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Status e prioridade
            $table->enum('status', ['planning', 'active', 'on_hold', 'completed', 'cancelled'])->default('planning');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            
            // Datas
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('deadline')->nullable();
            
            // Configurações do projeto
            $table->boolean('is_public')->default(false);
            $table->string('color', 7)->default('#3B82F6'); // Cor hex para identificação visual
            $table->string('icon')->nullable(); // Ícone do projeto
            
            // Orçamento (opcional)
            $table->decimal('budget', 10, 2)->nullable();
            $table->string('currency', 3)->default('BRL');
            
            // Progresso
            $table->integer('progress_percentage')->default(0);
            
            // Metadados
            $table->json('settings')->nullable(); // Configurações específicas do projeto
            $table->json('metadata')->nullable(); // Dados adicionais
            
            $table->timestamps();
            
            // Índices para performance
            $table->index(['user_id', 'status']);
            $table->index(['status', 'priority']);
            $table->index('deadline');
            $table->index('slug');
            $table->index('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};