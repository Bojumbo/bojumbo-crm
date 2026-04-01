<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Automation extends Model
{
    protected $fillable = [
        'pipeline_id',
        'pipeline_stage_id',
        'action_type',
        'action_payload',
        'is_active',
    ];

    protected $casts = [
        'action_payload' => 'array',
        'is_active' => 'boolean',
    ];

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class , 'pipeline_stage_id');
    }
}
