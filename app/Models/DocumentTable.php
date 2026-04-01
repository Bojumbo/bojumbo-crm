<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTable extends Model
{
    protected $fillable = ['name', 'columns', 'show_total', 'styles'];

    protected $casts = [
        'columns' => 'array',
        'styles' => 'array',
        'show_total' => 'boolean'
    ];
}
