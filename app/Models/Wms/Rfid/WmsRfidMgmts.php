<?php

namespace App\Models\Wms\Rfid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WmsRfidMgmts extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'organisation_id',
        'group',
        'created_by',
        'updated_by',
    ];
}
