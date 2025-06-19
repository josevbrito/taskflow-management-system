<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garante que há usuários e projetos suficientes antes de criar tarefas
        if (User::count() < 5) {
            User::factory(5)->create();
        }
        if (Project::count() < 3) {
            Project::factory(3)->create([
                'user_id' => User::inRandomOrder()->first()->id,
            ]);
        }
        Task::factory(50)->create();
    }
}
