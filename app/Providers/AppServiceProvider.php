<?php

namespace App\Providers;

use App\Models\StockActive;
use App\Observers\StockActiveObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Daftarkan Observer secara eksplisit
        StockActive::observe(StockActiveObserver::class);
    }
}
