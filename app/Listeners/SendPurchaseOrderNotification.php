<?php

namespace App\Listeners;

use App\Events\PurchaseOrderCreated;
use App\Models\User;
use App\Notifications\PurchaseOrderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPurchaseOrderNotification implements ShouldQueue
{
    public function handle(PurchaseOrderCreated $event): void
    {
        // Kirim ke OWNER untuk approval
        User::where('role', 'OWNER')->each(function ($admin) use ($event) {
            $admin->notify(new PurchaseOrderNotification($event->order));
        });
    }
}
