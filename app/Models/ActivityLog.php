<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'loggable_type',
        'loggable_id',
        'static_id',
        'old_value',
        'new_value',
        'action'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    public function fieldMetadata(): BelongsTo
    {
        return $this->belongsTo(FieldMetadata::class , 'static_id', 'static_id');
    }
}
