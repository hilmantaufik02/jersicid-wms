<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\User;
use App\Notifications\UserRegisteredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUserRegisteredNotification implements ShouldQueue
{
    public function handle(UserRegistered $event): void
    {
        // Kirim notifikasi ke semua OWNER
        User::where('role', 'OWNER')->each(function ($admin) use ($event) {
            $admin->notify(new UserRegisteredNotification($event->user));
        });
    }
}
