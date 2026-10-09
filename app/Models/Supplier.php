<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'supplier_code',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'city',
        'province',
        'postal_code',
        'npwp',
        'type',
        'status',
        'notes',
        'created_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {
            if (empty($supplier->id)) {
                $supplier->id = (string) Str::uuid();
            }

            // Auto-generate supplier code jika kosong
            if (empty($supplier->supplier_code)) {
                $count = Supplier::count() + 1;
                $supplier->supplier_code = 'SUP-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relasi
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_name', 'name');
    }

    // Helper
    public function getStatusColorAttribute()
    {
        return $this->status === 'AKTIF'
            ? 'bg-emerald-500/20 text-emerald-400'
            : 'bg-slate-500/20 text-slate-400';
    }

    public function getTypeColorAttribute()
    {
        return match ($this->type) {
            'SUBLIMASI' => 'bg-purple-500/20 text-purple-400',
            'KONVEKSI' => 'bg-pink-500/20 text-pink-400',
            'UMUM' => 'bg-slate-500/20 text-slate-400',
            default => 'bg-slate-500/20 text-slate-400',
        };
    }
}
