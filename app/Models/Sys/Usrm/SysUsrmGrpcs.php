<?php

namespace App\Models\Sys\Usrm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class SysUsrmGrpcs extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'organisation_id',
        'created_by',
        'updated_by',
    ];
}
