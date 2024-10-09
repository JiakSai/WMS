<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Rfid extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'warehouse_id',
        'group',
        'created_by',
        'updated_by',
        'is_deleted',
    ];
}
