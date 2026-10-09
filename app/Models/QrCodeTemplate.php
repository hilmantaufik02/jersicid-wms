<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCodeTemplate extends Model
{
    protected $fillable = [
        'name',
        'type',
        'template_data',
        'is_default',
    ];

    protected $casts = [
        'template_data' => 'array',
        'is_default' => 'boolean',
    ];

    public static function getDefault($type)
    {
        return static::where('type', $type)
            ->where('is_default', true)
            ->first();
    }
}
