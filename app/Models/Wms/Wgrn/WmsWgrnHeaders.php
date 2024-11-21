<?php

namespace App\Models\Wms\Wgrn;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WmsWgrnHeaders extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'receipt',
        'warehouse_id',
        'bill',
        'ship',
        'packing_slip',
        'packing_slip_file',
        'do',
        'do_file',
        'invoice',
        'invoice_file',
        'receipt_date',
        'organisation_id',
        'created_by',
        'updated_by'
    ];
}
