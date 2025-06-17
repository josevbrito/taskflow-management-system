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
        Schema::create('time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Tempo
            $table->datetime('start_time');
            $table->datetime('end_time')->nullable();
            $table->integer('duration_minutes')->nullable(); // Duração em minutos
            
            // Descrição do trabalho realizado
            $table->text('description')->nullable();
            
            // Faturamento
            $table->boolean('is_billable')->default(true);
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            
            // Status
            $table->enum('status', ['running', 'stopped', 'approved', 'rejected'])->default('running');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Metadados
            $table->json('metadata')->nullable(); // Dados extras como localização, device, etc.
            
            $table->timestamps();
            
            // Índices
            $table->index(['task_id', 'user_id']);
            $table->index(['user_id', 'start_time']);
            $table->index(['start_time', 'end_time']);
            $table->index('status');
            $table->index('is_billable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_logs');
    }
};