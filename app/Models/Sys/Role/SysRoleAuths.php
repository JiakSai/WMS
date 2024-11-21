<?php

namespace App\Models\Sys\Role;

use App\Models\Sys\Usrm\SysUsrmRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SysRoleAuths extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'main_module',
        'name',
        'description',
        'organisation_id',
        'created_by',
        'updated_by'
    ];
}
