<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Counterparty extends Model
{
    use SoftDeletes;
    protected $fillable = ['type'];
    /**
     * Отримати всі значення полів цього контрагента.
     */
    public function fieldValues(): HasMany
    {
        return $this->hasMany(CounterpartyFieldValue::class);
    }
    /**
     * Зручний хелпер для отримання значення за Static ID.
     * Використання: $counterparty->getFieldValue(1001)
     */
    public function getFieldValue(int $staticId)
    {
        return $this->fieldValues->where('static_id', $staticId)->first()?->value;
    }
}