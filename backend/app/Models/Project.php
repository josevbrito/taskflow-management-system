<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'status',
        'priority',
        'start_date',
        'end_date',
        'budget',
        'slug',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($project) {
            $project->slug = Str::slug($project->name);
            $originalSlug = $project->slug;
            $count = 1;
            while (static::where('slug', $project->slug)->exists()) {
                $project->slug = $originalSlug . '-' . $count++;
            }
        });
        static::updating(function ($project) {
            if ($project->isDirty('name')) {
                $project->slug = Str::slug($project->name);
                $originalSlug = $project->slug;
                $count = 1;
                while (static::where('slug', $project->slug)->where('id', '!=', $project->id)->exists()) {
                    $project->slug = $originalSlug . '-' . $count++;
                }
            }
        });
    }

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class);
    }
}