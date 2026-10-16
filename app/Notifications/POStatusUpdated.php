<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class POStatusUpdated extends Notification
{
    use Queueable;
    protected $po;
    protected $oldStatus;
    protected $newStatus;

    public function __construct($po, $oldStatus, $newStatus)
    {
        $this->po = $po;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['database']; // Simpan di tabel notifications
    }

    public function toDatabase($notifiable)
    {
        return [
            'po_id' => $this->po->id,
            'po_number' => $this->po->po_number,
            'vendor_name' => $this->po->vendor?->name ?? 'Unknown',
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "PO {$this->po->po_number} ({$this->po->vendor?->name}) status berubah dari {$this->oldStatus} menjadi {$this->newStatus}.",
            'link' => route('po.show', $this->po->id),
            'type' => $this->newStatus === 'READY_FOR_PICKUP' ? 'success' : 'info',
        ];
    }
}
