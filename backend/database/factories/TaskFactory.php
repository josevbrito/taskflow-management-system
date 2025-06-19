<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $projectId = Project::inRandomOrder()->first()->id ?? Project::factory()->create()->id;
        $assignedToUserId = User::inRandomOrder()->first()->id ?? User::factory()->create()->id;
        $createdByUserId = User::inRandomOrder()->first()->id ?? User::factory()->create()->id;

        return [
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'project_id' => $projectId,
            'assigned_to' => $assignedToUserId,
            'created_by' => $createdByUserId,
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'cancelled']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'completed_at' => $this->faker->boolean(20) ? $this->faker->dateTimeBetween('-1 year', 'now') : null, // 20% de chance de ser concluída
        ];
    }
}
