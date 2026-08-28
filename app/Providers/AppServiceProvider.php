<?php

namespace App\Providers;

use App\Models\InformationModel;
use App\Policies\InformationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\InformationRepositoryInterface::class,
            \App\Repositories\EloquentInformationRepository::class,
        );

        $this->app->bind(
            \App\Services\FileStorage\InformationFileUploaderInterface::class,
            \App\Services\FileStorage\InformationFileUploader::class,
        );
    }

    public function boot(): void
    {
        Gate::policy(InformationModel::class, InformationPolicy::class);
    }
}
