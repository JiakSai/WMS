<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class MainModule extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'code',
        'icon',
        'created_by',
        'updated_by',
        'is_deleted',
        'is_active'
    ];
}