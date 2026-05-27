<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    /**
     * Catat aktivitas.
     *
     * @param string $action Contoh: 'created', 'updated', 'deleted', 'payment_paid'
     * @param string $description Deskripsi singkat
     * @param Model|null $subject Model yang terkait (polymorphic)
     * @param array|null $properties Data tambahan
     */
    public function log(
        string $action,
        string $description,
        ?Model $subject = null,
        ?array $properties = null
    ): ActivityLog {
        return ActivityLog::create([
            'shop_id' => auth()->check() ? auth()->user()->shop_id : null,
            'user_id' => auth()->id(),
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
            'action' => $action,
            'description' => $description,
            'properties' => $properties,
        ]);
    }
}
