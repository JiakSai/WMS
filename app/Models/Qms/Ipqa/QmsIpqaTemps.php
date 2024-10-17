<?php

namespace App\Models\Qms\Ipqa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QmsIpqaTemps extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'created_by',
        'updated_by',
        'date_upload',      // Add date_upload field
        'version_name',     // Add version_name field
        'folder_key',
        'file_name',        // Add file_name field
        'status'            // Add status field
    ];
}
