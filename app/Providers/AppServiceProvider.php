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

use App\Repositories\DeviceRepository;
use App\Repositories\Contracts\DeviceRepositoryInterface;
use App\Services\DeviceTransferService;
use App\Services\Contracts\DeviceTransferServiceInterface;

use App\Repositories\MonitorRepository;
use App\Repositories\Contracts\MonitorRepositoryInterface;
use App\Services\MonitorTransferService;
use App\Services\Contracts\MonitorTransferServiceInterface;

use App\Repositories\NetworkDeviceRepository;
use App\Repositories\Contracts\NetworkDeviceRepositoryInterface;
use App\Services\NetworkDeviceTransferService;
use App\Services\Contracts\NetworkDeviceTransferServiceInterface;

use App\Repositories\PrinterRepository;
use App\Repositories\Contracts\PrinterRepositoryInterface;
use App\Services\PrinterTransferService;
use App\Services\Contracts\PrinterTransferServiceInterface;

use App\Repositories\TelephoneRepository;
use App\Repositories\Contracts\TelephoneRepositoryInterface;
use App\Services\TelephoneTransferService;
use App\Services\Contracts\TelephoneTransferServiceInterface;

use App\Repositories\TouchpanelRepository;
use App\Repositories\Contracts\TouchpanelRepositoryInterface;
use App\Services\TouchpanelTransferService;
use App\Services\Contracts\TouchpanelTransferServiceInterface;

use App\Services\Contracts\RoomServiceInterface;
use App\Services\RoomApiService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Shared
        $this->app->bind(WarehouseRepositoryInterface::class,       WarehouseRepository::class);
        $this->app->bind(InventoryNumberRepositoryInterface::class,  InventoryNumberRepository::class);
        $this->app->bind(RoomServiceInterface::class,               RoomApiService::class);

        // Computer
        $this->app->bind(ComputerRepositoryInterface::class,        ComputerRepository::class);
        $this->app->bind(ComputerTransferServiceInterface::class,   ComputerTransferService::class);

        // Device
        $this->app->bind(DeviceRepositoryInterface::class,          DeviceRepository::class);
        $this->app->bind(DeviceTransferServiceInterface::class,     DeviceTransferService::class);

        // Monitor
        $this->app->bind(MonitorRepositoryInterface::class,         MonitorRepository::class);
        $this->app->bind(MonitorTransferServiceInterface::class,    MonitorTransferService::class);

        // NetworkDevice
        $this->app->bind(NetworkDeviceRepositoryInterface::class,   NetworkDeviceRepository::class);
        $this->app->bind(NetworkDeviceTransferServiceInterface::class, NetworkDeviceTransferService::class);

        // Printer
        $this->app->bind(PrinterRepositoryInterface::class,         PrinterRepository::class);
        $this->app->bind(PrinterTransferServiceInterface::class,    PrinterTransferService::class);

        // Telephone
        $this->app->bind(TelephoneRepositoryInterface::class,       TelephoneRepository::class);
        $this->app->bind(TelephoneTransferServiceInterface::class,  TelephoneTransferService::class);

        // Touchpanel
        $this->app->bind(TouchpanelRepositoryInterface::class,      TouchpanelRepository::class);
        $this->app->bind(TouchpanelTransferServiceInterface::class, TouchpanelTransferService::class);
    }

    public function boot(): void
    {
        //
    }
}
