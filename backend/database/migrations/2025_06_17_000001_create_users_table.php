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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Campos para roles e permissões
            $table->enum('role', ['admin', 'manager', 'user'])->default('user');
            $table->boolean('is_active')->default(true);
            
            // Campos para 2FA
            $table->boolean('is_2fa_enabled')->default(false);
            $table->string('google2fa_secret')->nullable();
            $table->timestamp('google2fa_verified_at')->nullable();
            
            // Campos para profile
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->string('phone')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            
            // Campos para controle de acesso
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->integer('login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            
            $table->rememberToken();
            $table->timestamps();
            
            // Índices para performance
            $table->index(['email', 'is_active']);
            $table->index(['role', 'is_active']);
            $table->index('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};