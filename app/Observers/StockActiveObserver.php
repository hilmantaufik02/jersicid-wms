<?php

namespace App\Observers;

use App\Models\StockActive;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;

class StockActiveObserver
{
    /**
     * Catat saat stok pertama kali dibuat (Inbound pertama kali di bin tertentu).
     */
    public function created(StockActive $stock): void
    {
        $this->logActivity('CREATE', $stock, null, $stock->getAttributes());
    }

    /**
     * Catat saat ada pergerakan stok (qty berubah) atau pindah bin.
     */
    public function updated(StockActive $stock): void
    {
        // Hanya log jika ada perubahan pada kolom krusial
        if ($stock->isDirty(['qty', 'reserved_qty', 'bin_code'])) {
            $this->logActivity('UPDATE', $stock, $stock->getOriginal(), $stock->getDirty());
        }
    }

    /**
     * Catat saat stok di bin dihapus (misal: bin direlokasi total).
     */
    public function deleted(StockActive $stock): void
    {
        $this->logActivity('DELETE', $stock, $stock->getOriginal(), null);
    }

    /**
     * Helper untuk menulis ke tabel audit_trails.
     */
    private function logActivity(string $action, StockActive $stock, ?array $oldValues, ?array $newValues): void
    {
        // Filter hanya kolom yang relevan untuk audit agar JSON tidak bengkak
        $relevantKeys = ['sku', 'bin_code', 'qty', 'reserved_qty', 'batch_no'];
        
        $filteredOld = $oldValues ? array_intersect_key($oldValues, array_flip($relevantKeys)) : null;
        $filteredNew = $newValues ? array_intersect_key($newValues, array_flip($relevantKeys)) : null;

        AuditTrail::create([
            'user_id'    => Auth::id(), // Akan null jika dijalankan via CLI/Cron
            'action'     => $action,
            'module'     => 'INVENTORY',
            'old_values' => $filteredOld,
            'new_values' => $filteredNew,
            'ip_address' => request()?->ip(),
        ]);
    }
}
