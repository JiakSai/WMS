<?php

namespace App\Models\Qms\Role;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class QmsRoleAsgns extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'created_by',
        'updated_by',
        'username',
        'role_id'
    ];
}
