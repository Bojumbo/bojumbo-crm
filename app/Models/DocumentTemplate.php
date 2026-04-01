<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    protected $fillable = ['name', 'google_drive_id', 'entity_type', 'orientation', 'content'];
}