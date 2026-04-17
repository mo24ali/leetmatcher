<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\BlogPost;
use App\Models\Interview;
use App\Models\Project;
use App\Models\User;
use App\Policies\ApplicationPolicy;
use App\Policies\BlogPostPolicy;
use App\Policies\InterviewPolicy;
use App\Policies\ProjectPolicy;
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
        Project::class => ProjectPolicy::class,
        Application::class => ApplicationPolicy::class,
        Interview::class => InterviewPolicy::class,
        BlogPost::class => BlogPostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gates
        Gate::define('isAdmin', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('isRecruiter', function (User $user) {
            return $user->role === 'recruiter';
        });

        Gate::define('isStudent', function (User $user) {
            // Mapping 'applicant' to student logic if needed, but the user assumed 'student'
            return $user->role === 'student' || $user->role === 'applicant';
        });
    }
}
