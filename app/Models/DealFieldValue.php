<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class DealFieldValue extends Model
{
    protected $fillable = ['deal_id', 'static_id', 'value'];
    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}