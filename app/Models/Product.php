<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use SoftDeletes;
    public function fieldValues(): HasMany
    {
        return $this->hasMany(ProductFieldValue::class);
    }
    public function getFieldValue(int $staticId)
    {
        return $this->fieldValues->where('static_id', $staticId)->first()?->value;
    }
}