<?php
namespace App\Models;

use App\Traits\HasDynamicFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, HasDynamicFields;

    public function fieldValues(): HasMany
    {
        return $this->hasMany(ProductFieldValue::class);
    }
    // getFieldValue now handled by trait
}