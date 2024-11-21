<?php

namespace App\Models\Qms\Ipqa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QmsIpqaTemps extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'created_by',
        'updated_by',
        'date_upload',      // Add date_upload field
        'version_name',     // Add version_name field
        'folder_key',
        'remark',
        'categories',
        'file_name',        // Add file_name field
        'status'            // Add status field
    ];
}
