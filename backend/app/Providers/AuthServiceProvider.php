<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        // EU TENHO QUE LEMBRAR DE ADICIONAR AS OUTRAS (Project::class => ProjectPolicy::class)
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define um "gate" simples para verificar se o usuário é admin
        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });
    }
}