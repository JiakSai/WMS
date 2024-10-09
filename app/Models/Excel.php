<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Excel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'warehouse_id',
        'created_by',
        'updated_by',
    ];
    
}
