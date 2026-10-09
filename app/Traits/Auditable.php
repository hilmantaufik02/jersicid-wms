<?php

namespace App\Traits;

use App\Models\AuditLog;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait Auditable
{
    protected static function bootAuditable()
    {
        call_user_func([static::class, 'created'], function ($model) {
            AuditLog::log('create', get_class($model), $model->id, null, $model->toArray());
        });

        call_user_func([static::class, 'updated'], function ($model) {
            AuditLog::log('update', get_class($model), $model->id, $model->getOriginal(), $model->toArray());
        });

        call_user_func([static::class, 'deleted'], function ($model) {
            AuditLog::log('delete', get_class($model), $model->id, $model->toArray(), null);
        });
    }
}
