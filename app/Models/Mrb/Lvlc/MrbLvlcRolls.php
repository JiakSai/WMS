<?php

namespace App\Models\Mrb\Lvlc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MrbLvlcRolls extends Model
{
    use HasFactory;
    protected $fillable = [
        'organisation_id',
        'created_by',
        'updated_by',
        'level',      // Add date_upload field
        'descriptions',     // Add version_name field
        'max_users'
    ];
}
