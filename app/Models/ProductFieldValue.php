<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ProductFieldValue extends Model
{
    protected $fillable = ['product_id', 'static_id', 'value'];
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}