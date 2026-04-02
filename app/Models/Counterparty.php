<?php
namespace App\Models;

use App\Traits\HasDynamicFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Counterparty extends Model
{
    use SoftDeletes, HasDynamicFields;
    protected $fillable = ['type'];
    /**
     * Отримати всі значення полів цього контрагента.
     */
    public function fieldValues(): HasMany
    {
        return $this->hasMany(CounterpartyFieldValue::class);
    }
    // getFieldValue now handled by trait
}