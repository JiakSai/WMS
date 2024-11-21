<?php

namespace App\Models\Wms\Whmg;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use function PHPSTORM_META\map;

class WmsWhmgLocts extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [

        'warehouse_id',
        'name',
        'organisation_id',
        'created_by',
        'updated_by'

    ];
}
