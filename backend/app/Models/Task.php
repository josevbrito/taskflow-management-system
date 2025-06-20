<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'project_id',
        'assigned_to',
        'created_by',
        'status',
        'priority',
        'due_date',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    // Relacionamentos
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

}