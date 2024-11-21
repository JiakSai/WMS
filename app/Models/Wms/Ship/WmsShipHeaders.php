<?php

namespace App\Models\Wms\Ship;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WmsShipHeaders extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shipment_no',
        'warehouse_id',
        'bill',
        'ship',
        'customs_slip',
        'customs_slip_file',
        'shipment_slip',
        'shipment_slip_file',
        'invoice',
        'invoice_file',
        'shipment_date',
        'organisation_id',
        'created_by',
        'updated_by'
    ];
}
