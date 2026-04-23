<?php

namespace App\Providers;
use App\Repositories\ComputerRepository;
use App\Repositories\Contracts\ComputerRepositoryInterface;
use App\Repositories\Contracts\InventoryNumberRepositoryInterface;
use App\Repositories\Contracts\WarehouseRepositoryInterface;
use App\Repositories\InventoryNumberRepository;
use App\Repositories\WarehouseRepository;
use App\Services\ComputerTransferService;
use App\Services\Contracts\ComputerTransferServiceInterface;
use App\Services\Contracts\RoomServiceInterface;
use App\Services\RoomApiService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ComputerRepositoryInterface::class,       ComputerRepository::class);
        $this->app->bind(WarehouseRepositoryInterface::class,      WarehouseRepository::class);
        $this->app->bind(InventoryNumberRepositoryInterface::class, InventoryNumberRepository::class);
        $this->app->bind(RoomServiceInterface::class,              RoomApiService::class);
        $this->app->bind(ComputerTransferServiceInterface::class,  ComputerTransferService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
