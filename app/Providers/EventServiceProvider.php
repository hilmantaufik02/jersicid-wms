<?php

namespace App\Providers;

use App\Events\PurchaseOrderCreated;
use App\Events\StockMovementCreated;
use App\Events\UserRegistered;
use App\Listeners\SendPurchaseOrderNotification;
use App\Listeners\SendStockMovementNotification;
use App\Listeners\SendUserRegisteredNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserRegistered::class => [
            SendUserRegisteredNotification::class,
        ],
        StockMovementCreated::class => [
            SendStockMovementNotification::class,
        ],
        PurchaseOrderCreated::class => [
            SendPurchaseOrderNotification::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
