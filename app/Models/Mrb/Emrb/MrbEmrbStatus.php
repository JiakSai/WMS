<?php

namespace App\Models\Mrb\Emrb;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MrbEmrbStatus extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'id',
        'organisation_id',
        'created_by',
        'updated_by',
        'form_id',
        'level',
        'remark',
        'status',
    ];
}
