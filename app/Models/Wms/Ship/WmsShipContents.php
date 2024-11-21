<?php

namespace App\Models\Wms\Ship;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WmsShipContents extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ship_header_id',
        'serial_number',
        'item_code',
        'mpn',
        'location',
        'lot',
        'manufacture_date',
        'quantity',
        'uom',
        'status',
        'organisation_id',
        'created_by',
        'updated_by'
    ];
}
