<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Module extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'group',
        'name',
        'code',
        'description',
        'route',
        'mobile',
        'icon',
        'warehouse_id',
        'created_by',
        'updated_by',
        'is_deleted',
        'is_active'
    ];
}
