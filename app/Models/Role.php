<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'user_id',
        'organisation_id',
        'main_module',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'user_id' => 'array',
    ];
}
