<?php

namespace App\Models\Wms\Role;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WmsRolePerms extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
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
