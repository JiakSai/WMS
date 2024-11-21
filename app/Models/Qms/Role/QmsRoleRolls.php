<?php

namespace App\Models\Qms\Role;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class QmsRoleRolls extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'organisation_id',
        'created_by',
        'updated_by',
        'role_name',
        'remark'
    ];
}
