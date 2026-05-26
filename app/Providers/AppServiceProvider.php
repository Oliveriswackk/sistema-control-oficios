<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\Oficio;
use App\Policies\OficioPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Oficio::class, OficioPolicy::class);
    }
}
