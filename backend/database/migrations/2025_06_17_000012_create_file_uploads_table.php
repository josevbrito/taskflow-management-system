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
        Schema::create('file_uploads', function (Blueprint $table) {
            $table->id();
            
            // Relacionamento polimórfico (pode ser anexado a tasks, projects, comments, etc.)
            $table->morphs('attachable');
            
            // Informações do arquivo
            $table->string('original_name');
            $table->string('filename'); // Nome do arquivo no storage
            $table->string('path'); // Caminho completo
            $table->string('disk')->default('public'); // Disco de armazenamento
            $table->string('mime_type');
            $table->bigInteger('size'); // Tamanho em bytes
            
            // Hash para verificar integridade
            $table->string('hash')->nullable();
            
            // Metadados específicos para diferentes tipos
            $table->json('metadata')->nullable(); // Dimensões para imagens, duração para vídeos, etc.
            
            // Controle de acesso
            $table->boolean('is_public')->default(false);
            $table->json('allowed_users')->nullable(); // IDs dos usuários com acesso
            
            // Quem fez o upload
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            
            // Organização
            $table->string('category')->nullable(); // document, image, video, etc.
            $table->json('tags')->nullable();
            
            // Controle de versão
            $table->foreignId('parent_file_id')->nullable()->constrained('file_uploads')->onDelete('cascade');
            $table->integer('version')->default(1);
            
            // Status
            $table->enum('status', ['uploading', 'processing', 'ready', 'failed'])->default('uploading');
            $table->text('processing_error')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['uploaded_by', 'created_at']);
            $table->index(['mime_type', 'category']);
            $table->index('status');
            $table->index('hash');
            $table->index('parent_file_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_uploads');
    }
};