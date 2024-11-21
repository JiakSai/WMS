<?php

namespace App\Models\Sys\Usrm;

use App\Models\Sys\Role\SysRoleAuths;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SysUsrmRoles extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'permissions',
        'organisation_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Role has many users.
     */
    public function users()
    {
        return $this->hasMany(SysUsrmUsers::class, 'role');
    }

    /**
     * Get the permissions associated with the role.
     *
     * @return array
     */
    public function getPermissionsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Set the permissions for the role.
     *
     * @param array $value
     */
    public function setPermissionsAttribute($value)
    {
        $this->attributes['permissions'] = json_encode($value);
    }

    /**
     * Get the permission objects associated with the role.
     */
    public function permissionObjects()
    {
        return SysRoleAuths::whereIn('id', $this->permissions)->get();
    }

}
