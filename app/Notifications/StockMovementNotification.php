<?php

namespace App\Notifications;

use App\Models\StockMovement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StockMovementNotification extends Notification
{
    use Queueable;

    public StockMovement $movement;

    public function __construct(StockMovement $movement)
    {
        $this->movement = $movement;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $actionLabels = [
            'INBOUND' => 'Barang Masuk',
            'OUTBOUND' => 'Barang Keluar',
            'TRANSFER' => 'Transfer Stok',
            'ADJUSTMENT' => 'Penyesuaian Stok',
        ];

        return [
            'type' => 'stock_movement',
            'title' => $actionLabels[$this->movement->type] ?? 'Pergerakan Stok',
            'message' => "SKU {$this->movement->sku}: {$this->movement->qty} unit {$this->movement->type} di bin {$this->movement->bin_code}",
            'sku' => $this->movement->sku,
            'qty' => $this->movement->qty,
            'bin_code' => $this->movement->bin_code,
            'movement_type' => $this->movement->type,
        ];
    }
}
