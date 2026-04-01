<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Логує зміну конкретного динамічного поля.
     */
    public static function logFieldChange($model, int $staticId, $oldValue, $newValue)
    {
        // Якщо значення не змінилося — нічого не робимо
        if ($oldValue === $newValue) {
            return;
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'loggable_type' => get_class($model),
            'loggable_id' => $model->id,
            'static_id' => $staticId,
            'old_value' => is_array($oldValue) ? json_encode($oldValue) : $oldValue,
            'new_value' => is_array($newValue) ? json_encode($newValue) : $newValue,
            'action' => 'updated'
        ]);
    }

    /**
     * Логує створення або видалення сутності.
     */
    public static function logAction($model, string $action)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'loggable_type' => get_class($model),
            'loggable_id' => $model->id,
            'static_id' => null,
            'action' => $action
        ]);
    }

    /**
     * Логує коментар користувача.
     */
    public static function logComment($model, string $comment)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'loggable_type' => get_class($model),
            'loggable_id' => $model->id,
            'static_id' => null,
            'new_value' => $comment,
            'action' => 'comment'
        ]);
    }
}
