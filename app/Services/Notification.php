<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'type', 'title', 'message', 'details',
        'user_id', 'related_type', 'related_id',
        'is_read', 'read_at',
    ];

    protected $casts = [
        'details' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function related()
    {
        return $this->morphTo();
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function getIconAttribute(): string
    {
        $icons = [
            'auth' => '🔐',
            'product' => '📦',
            'order' => '',
            'stock' => '📊',
            'import' => '📥',
            'pos' => '🛒',
            'qr' => '',
            'user' => '👤',
            'category' => '🏷️',
            'supplier' => '🏭',
        ];

        return $icons[$this->type] ?? '📌';
    }

    public function getTypeColorAttribute(): string
    {
        $colors = [
            'auth' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
            'product' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
            'order' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
            'stock' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
            'import' => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20',
            'pos' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
            'qr' => 'bg-pink-500/10 text-pink-400 border-pink-500/20',
            'user' => 'bg-red-500/10 text-red-400 border-red-500/20',
            'category' => 'bg-teal-500/10 text-teal-400 border-teal-500/20',
            'supplier' => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
        ];

        return $colors[$this->type] ?? 'bg-gray-500/10 text-gray-400 border-gray-500/20';
    }
}
