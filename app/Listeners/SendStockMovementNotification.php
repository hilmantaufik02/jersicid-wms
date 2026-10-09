<?php

namespace App\Listeners;

use App\Events\StockMovementCreated;
use App\Models\User;
use App\Notifications\StockMovementNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendStockMovementNotification implements ShouldQueue
{
    public function handle(StockMovementCreated $event): void
    {
        // Kirim ke OWNER dan SUPERVISOR
        User::whereIn('role', ['OWNER', 'SUPERVISOR'])->each(function ($user) use ($event) {
            $user->notify(new StockMovementNotification($event->movement));
        });
    }
}
