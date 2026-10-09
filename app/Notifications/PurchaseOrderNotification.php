<?php

namespace App\Notifications;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PurchaseOrderNotification extends Notification
{
    use Queueable;

    public PurchaseOrder $order;

    public function __construct(PurchaseOrder $order)
    {
        $this->order = $order;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'purchase_order',
            'title' => 'Purchase Order Baru',
            'message' => "PO {$this->order->po_number} dari {$this->order->supplier_name} menunggu approval.",
            'po_id' => $this->order->id,
            'po_number' => $this->order->po_number,
            'supplier' => $this->order->supplier_name,
            'action_url' => route('po.show', $this->order->id),
        ];
    }
}
