<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    const TYPE_AUTH = 'auth';

    const TYPE_PRODUCT = 'product';

    const TYPE_ORDER = 'order';

    const TYPE_STOCK = 'stock';

    const TYPE_IMPORT = 'import';

    const TYPE_POS = 'pos';

    const TYPE_QR = 'qr';

    const TYPE_USER = 'user';

    const TYPE_CATEGORY = 'category';

    const TYPE_SUPPLIER = 'supplier';

    public static function log(string $type, string $action, string $description, ?User $user = null, array $metadata = [], $relatedModel = null): Notification
    {
        return Notification::create([
            'type' => $type,
            'title' => self::getActionTitle($action),
            'message' => $description,
            'details' => array_merge($metadata, [
                'action' => $action,
                'ip' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
            ]),
            'user_id' => $user?->id ?? auth()->id(),
            'related_type' => $relatedModel ? get_class($relatedModel) : null,
            'related_id' => $relatedModel?->id,
            'is_read' => false,
        ]);
    }

    private static function getActionTitle(string $action): string
    {
        $titles = [
            'login' => 'Login',
            'logout' => 'Logout',
            'create' => 'Membuat',
            'update' => 'Memperbarui',
            'delete' => 'Menghapus',
            'import' => 'Import',
            'approve' => 'Menyetujui',
            'reject' => 'Menolak',
            'cancel' => 'Membatalkan',
            'checkout' => 'Checkout',
            'picking' => 'Picking',
            'opname' => 'Stock Opname',
            'generate_qr' => 'Generate QR',
        ];

        return $titles[$action] ?? ucfirst($action);
    }

    public static function auth(string $action, string $description, array $metadata = [])
    {
        return self::log(self::TYPE_AUTH, $action, $description, null, $metadata);
    }

    public static function product(string $action, string $description, $related = null, array $metadata = [])
    {
        return self::log(self::TYPE_PRODUCT, $action, $description, null, $metadata, $related);
    }

    public static function order(string $action, string $description, $related = null, array $metadata = [])
    {
        return self::log(self::TYPE_ORDER, $action, $description, null, $metadata, $related);
    }

    public static function stock(string $action, string $description, $related = null, array $metadata = [])
    {
        return self::log(self::TYPE_STOCK, $action, $description, null, $metadata, $related);
    }

    public static function import(string $description, array $metadata = [])
    {
        return self::log(self::TYPE_IMPORT, 'import', $description, null, $metadata);
    }

    public static function pos(string $description, array $metadata = [])
    {
        return self::log(self::TYPE_POS, 'checkout', $description, null, $metadata);
    }

    public static function qr(string $description, array $metadata = [])
    {
        return self::log(self::TYPE_QR, 'generate_qr', $description, null, $metadata);
    }

    public static function user(string $action, string $description, $related = null, array $metadata = [])
    {
        return self::log(self::TYPE_USER, $action, $description, null, $metadata, $related);
    }

    public static function category(string $action, string $description, $related = null, array $metadata = [])
    {
        return self::log(self::TYPE_CATEGORY, $action, $description, null, $metadata, $related);
    }

    public static function supplier(string $action, string $description, $related = null, array $metadata = [])
    {
        return self::log(self::TYPE_SUPPLIER, $action, $description, null, $metadata, $related);
    }

    public static function getUserProductivity(?string $period = 'today')
    {
        $query = Notification::where('type', '!=', self::TYPE_AUTH)
            ->select('user_id', \DB::raw('count(*) as total_actions'))
            ->groupBy('user_id');

        self::applyPeriodFilter($query, $period);

        return $query->orderByDesc('total_actions')->get()->map(function ($item) {
            $user = User::find($item->user_id);

            return [
                'user' => $user,
                'total_actions' => $item->total_actions,
            ];
        });
    }

    private static function applyPeriodFilter($query, string $period): void
    {
        if ($period === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($period === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($period === 'month') {
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
        }
    }
}
