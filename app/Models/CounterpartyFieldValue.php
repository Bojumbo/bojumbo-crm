<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounterpartyFieldValue extends Model
{
    protected $fillable = ['counterparty_id', 'static_id', 'value'];

    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class);
    }

    /**
     * Посилання на метадані поля
     */
    public function metadata(): BelongsTo
    {
        return $this->belongsTo(FieldMetadata::class , 'static_id', 'static_id');
    }
}
