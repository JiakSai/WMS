<?php

namespace App\Models\Sys\Usrm;

use App\Models\Sys\Orga\SysOrgaCtrls;
use App\Models\Sys\Role\SysRoleAuths;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;

class SysUsrmUsers extends Authenticatable
{
    use HasFactory, Notifiable;

        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     protected $fillable = [
        'username',
        'password',
        'phone_number',
        'otp_code',
        'otp_expiry',
        'email_address',
        'name',
        'ticket',
        'telegram_id',
        'group',
        'role',
        'default_organisation',
        'main_modules_permission',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'main_modules_permission' => 'array', 
        ];
    }

    public function organisations()
    {
        return $this->belongsToMany(SysOrgaCtrls::class);
    }

    
}
