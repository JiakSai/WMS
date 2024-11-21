<?php

namespace App\Models\Wms\Wgrn;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WmsWgrnContents extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'wgrn_header_id',
        'serial_number',
        'item_code',
        'mpn',
        'location',
        'lot',
        'manufacture_date',
        'quantity',
        'uom',
        'organisation_id',
        'created_by',
        'updated_by'
    ];
}
