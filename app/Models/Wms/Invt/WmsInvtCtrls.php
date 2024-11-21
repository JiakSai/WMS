<?php

namespace App\Models\Wms\Invt;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class WmsInvtCtrls extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_code',
        'warehouse_id',
        'warehouse_location_id',
        'lot',
        'quantity',
        'uom',
        'manufacture_date',
        'organisation_id',
        'created_by',
        'updated_by',
    ];
}
