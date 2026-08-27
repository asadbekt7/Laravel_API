<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\InformationModel;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\InformationRepositoryInterface::class,
            \App\Repositories\EloquentInformationRepository::class,
        );
    }
    public function boot(): void
    {
        //
    }
    protected $policies = [
        InformationModel::class => \App\Policies\InformationPolicy::class,
    ];
}
