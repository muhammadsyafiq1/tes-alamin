<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        Gate::define('aksesBPRS', function (User $user) {
            return $user->status === 'BPRS';
            //Pakai return true untuk akses semua
        });

        Gate::define('aksesAdmin', function (User $user) {
            return $user->status === 'admin';
        });
    }
}
