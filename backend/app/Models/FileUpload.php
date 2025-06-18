<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    use HasFactory;
    protected $fillable = ['task_id', 'project_id', 'user_id', 'file_path', 'file_name', 'file_type', 'file_size'];
    public function task() { return $this->belongsTo(Task::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function user() { return $this->belongsTo(User::class); }
}