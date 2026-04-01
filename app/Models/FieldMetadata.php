<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for the numeric field-first architecture core.
 */
class FieldMetadata extends Model
{
    protected $table = 'fields_metadata';

    protected $fillable = [
        'static_id',
        'entity',
        'field_key',
        'field_type',
        'is_system',
        'label_en',
        'label_uk',
        'label_he',
    ];

    protected $casts = [
        'static_id' => 'integer',
        'is_system' => 'boolean',
    ];
}
