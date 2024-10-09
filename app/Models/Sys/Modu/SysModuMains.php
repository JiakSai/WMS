<?php

namespace App\Models\Sys\Modu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class SysModuMains extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'code',
        'icon',
        'warehouse_id',
        'created_by',
        'updated_by',
        'is_active'
    ];
}
