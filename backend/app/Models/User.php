<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'is_2fa_enabled',
        'google2fa_secret',
        'google2fa_verified_at',
        'avatar',
        'bio',
        'phone',
        'department',
        'position',
        'last_login_at',
        'last_login_ip',
        'login_attempts',
        'locked_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_2fa_enabled' => 'boolean',
        'google2fa_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
    ];

    // Relacionamentos
    public function projects()
    {
        return $this->hasMany(Project::class, 'user_id');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class, 'user_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function fileUploads()
    {
        return $this->hasMany(FileUpload::class, 'user_id');
    }

    public function projectMemberships()
    {
        return $this->hasMany(ProjectMember::class, 'user_id');
    }

    // Métodos auxiliares
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }
}