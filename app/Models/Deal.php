<?php

namespace App\Models;

use App\Traits\HasDynamicFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use SoftDeletes, HasDynamicFields;
    protected $fillable = [
        'google_drive_folder_id',
    ];
    public function fieldValues(): HasMany
    {
        return $this->hasMany(DealFieldValue::class);
    }
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'deal_products')
                    ->withPivot(['quantity', 'price_at_sale'])
                    ->withTimestamps();
    }
    // getFieldValue now handled by trait
}