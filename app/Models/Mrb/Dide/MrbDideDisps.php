<?php

namespace App\Models\Mrb\Dide;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MrbDideDisps extends Model
{
    use HasFactory, SoftDeletes;    
    protected $fillable = [
        'organisation_id',
        'created_by',
        'updated_by',
        'name', 
        'descriptions', 
    ];
}
