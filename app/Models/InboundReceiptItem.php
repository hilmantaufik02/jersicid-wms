<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InboundReceiptItem extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'inbound_receipt_id',
        'sku',
        'qty_expected',
        'qty_received',
        'qty_rejected',
        'bin_code',
        'batch_no',
        'expiry_date',
        'notes',
    ];

    public function receipt()
    {
        return $this->belongsTo(InboundReceipt::class, 'inbound_receipt_id');
    }

    public function masterSku()
    {
        return $this->belongsTo(MasterSku::class, 'sku', 'sku');
    }
}
