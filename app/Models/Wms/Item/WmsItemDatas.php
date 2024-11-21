<?php

namespace App\Models\Wms\Item;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class WmsItemDatas extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'unit_set',
        'inventory_unit',
        'weight_unit',
        'weight',
        'organisation_id',
        'created_by',
        'updated_by',
    ];

    public function itemGroupChilds()
    {
        return $this->belongsToMany(WmsItemGrpcsChilds::class);
    }
}
