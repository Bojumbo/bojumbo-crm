<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PipelineStage extends Model
{
    protected $fillable = ['pipeline_id', 'name', 'color', 'sort_order', 'is_won', 'is_lost'];
    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function automations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Automation::class , 'pipeline_stage_id');
    }
}