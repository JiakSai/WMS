<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'main_module',
        'tab_module',
        'name',
        'description',
        'role',
        'organisation_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'role' => 'array',
    ];
}
