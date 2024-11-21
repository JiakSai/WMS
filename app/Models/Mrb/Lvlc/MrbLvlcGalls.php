<?php

namespace App\Models\Mrb\Lvlc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MrbLvlcGalls extends Model
{
    use HasFactory;    
    protected $fillable = [
        'organisation_id',
        'created_by',
        'updated_by',
        'group_id',      // Add date_upload field
        'username',     // Add version_name field
        'level_id',
    ];
}
