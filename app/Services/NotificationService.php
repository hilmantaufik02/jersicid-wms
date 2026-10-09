<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create activity notification
     */
    public static function activity(string $title, string $message, ?User $user = null, array $details = [], $relatedModel = null): Notification
    {
        return Notification::create([
            'type' => 'activity',
            'title' => $title,
            'message' => $message,
            'details' => $details,
            'user_id' => $user?->id,
            'related_type' => $relatedModel ? get_class($relatedModel) : null,
            'related_id' => $relatedModel?->id,
        ]);
    }

    /**
     * Create alert notification
     */
    public static function alert(string $title, string $message, ?User $user = null): Notification
    {
        return Notification::create([
            'type' => 'alert',
            'title' => $title,
            'message' => $message,
            'user_id' => $user?->id,
        ]);
    }

    /**
     * Create info notification
     */
    public static function info(string $title, string $message, ?User $user = null): Notification
    {
        return Notification::create([
            'type' => 'info',
            'title' => $title,
            'message' => $message,
            'user_id' => $user?->id,
        ]);
    }

    /**
     * Create success notification
     */
    public static function success(string $title, string $message, ?User $user = null): Notification
    {
        return Notification::create([
            'type' => 'success',
            'title' => $title,
            'message' => $message,
            'user_id' => $user?->id,
        ]);
    }

    /**
     * Create warning notification
     */
    public static function warning(string $title, string $message, ?User $user = null): Notification
    {
        return Notification::create([
            'type' => 'warning',
            'title' => $title,
            'message' => $message,
            'user_id' => $user?->id,
        ]);
    }

    /**
     * Get unread count for user
     */
    public static function getUnreadCount(?int $userId = null): int
    {
        return Notification::query()->where('is_read', false)
            ->when($userId, fn($q) => $q->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhere('related_user_id', $userId)
                    ->orWhereNull('user_id'); // Global notifications
            }))
            ->count();
    }

    /**
     * Get recent notifications for user
     */
    public static function getRecent(?int $userId = null, int $limit = 10)
    {
        return Notification::query()->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhere('related_user_id', $userId)
                ->orWhereNull('user_id');
        })
            ->latest()
            ->limit($limit)
            ->get();
    }
}
