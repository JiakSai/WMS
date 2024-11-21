<?php

namespace App\Models\Mrb\Emrb;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MrbEmrbContents extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'form_id',
        'part_number',
        'description',
        'location',
        'quantity',
        'unit_price',
        'amount',
        'currency',
        'defect',
        'disposition',
        'root_cause',
        'correction',
        'remark',
        'file_path',
    ];
}
