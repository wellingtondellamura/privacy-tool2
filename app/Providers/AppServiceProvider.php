<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Models\Project;
use App\Models\InspectionPublication;
use App\Policies\ProjectPolicy;
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
        Gate::policy(InspectionPublication::class, PublicationPolicy::class);

        Event::listen(
            Registered::class,
            SendEmailVerificationNotification::class,
        );

        Gate::define('access-admin', function (User $user) {
            return $user->is_admin;
        });
    }
}
