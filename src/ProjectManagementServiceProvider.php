<?php

namespace Prasso\ProjectManagement;

use Illuminate\Support\ServiceProvider;
use Prasso\ProjectManagement\Filament\Resources\ProjectResource;
use Filament\Facades\Filament;

class ProjectManagementServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/project_management.php', 'project-management');
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'project-management');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/project_management.php' => config_path('project_management.php'),
            ], 'project-management-config');
        }

        // Register Filament Resources
        Filament::serving(function () {
            Filament::registerResources([
                ProjectResource::class,
            ]);
        });
    }
}
