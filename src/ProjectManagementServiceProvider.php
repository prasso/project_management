<?php

namespace Prasso\ProjectManagement;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
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
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'prasso-pm');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/project_management.php' => config_path('project_management.php'),
                __DIR__ . '/../resources/views' => resource_path('views/vendor/prasso-pm'),
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
