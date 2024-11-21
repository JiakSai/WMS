<?php

namespace App\Models\Wms\Whmg;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class WmsWhmgWhmgs extends Model
{
    use HasFactory, Notifiable, SoftDeletes;
    // use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'description',
        'type',
        'organisation_id',
        'created_by',
        'updated_by',
    ];


}
