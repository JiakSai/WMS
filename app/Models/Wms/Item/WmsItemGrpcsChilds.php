<?php

namespace App\Models\Wms\Item;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class WmsItemGrpcsChilds extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'organisation_id',
        'created_by',
        'updated_by',
    ];

    public function items()
    {
        return $this->belongsToMany(WmsItemDatas::class);
    }
}
