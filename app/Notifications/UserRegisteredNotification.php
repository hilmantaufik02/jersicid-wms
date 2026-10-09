<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class UserRegisteredNotification extends Notification
{
    use Queueable;

    public User $newUser;

    public function __construct(User $user)
    {
        $this->newUser = $user;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'user_registered',
            'title' => 'User Baru Terdaftar',
            'message' => "{$this->newUser->name} ({$this->newUser->role}) telah terdaftar dan menunggu approval.",
            'user_id' => $this->newUser->id,
            'user_name' => $this->newUser->name,
            'user_role' => $this->newUser->role,
            'action_url' => route('users.index'),
        ];
    }
}
