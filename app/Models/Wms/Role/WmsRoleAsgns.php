<?php

namespace App\Models\Wms\Role;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WmsRoleAsgns extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'user_id',
        'organisation_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'user_id' => 'array',
    ];
}
