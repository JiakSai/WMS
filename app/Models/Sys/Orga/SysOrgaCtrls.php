<?php

namespace App\Models\Sys\Orga;

use App\Models\Sys\Usrm\SysUsrmUsers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class SysOrgaCtrls extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
    ];

    public function users(){
        return $this->belongsToMany(SysUsrmUsers::class);
    }
}
