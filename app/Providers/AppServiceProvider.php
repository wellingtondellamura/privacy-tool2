<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Models\Project;
use App\Models\User;
use App\Models\RoundBadge;
use App\Models\InspectionPublication;
use App\Policies\ProjectPolicy;
use App\Policies\RoundBadgePolicy;
use App\Policies\PublicationPolicy;

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
        Vite::prefetch(concurrency: 3);

        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(RoundBadge::class, RoundBadgePolicy::class);
        Gate::policy(InspectionPublication::class, PublicationPolicy::class);

        Gate::define('access-admin', function (User $user) {
            return $user->is_admin;
        });
    }
}
