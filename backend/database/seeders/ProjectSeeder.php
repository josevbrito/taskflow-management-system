<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::count() === 0) {
            User::factory()->create([
                'name' => 'Seeder User',
                'email' => 'seeder@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
        }

        // Cria 10 projetos e atribui-os aleatoriamente a usuários existentes.
        Project::factory(10)->create();
    }
}
