<?php

namespace App\Models\Sys\Modu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SysModuSubms extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'code',
        'group',
        'description',
        'icon',
        'mobile',
        'route',
        'organisation_id',
        'created_by',
        'updated_by',
        'is_active'
    ];
}
