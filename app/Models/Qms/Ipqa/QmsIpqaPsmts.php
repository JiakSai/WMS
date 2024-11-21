<?php

namespace App\Models\Qms\Ipqa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QmsIpqaPsmts extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'created_by',
        'updated_by',
        'date',            // Date column
        'wo',              // Work order
        'customer',        // Customer
        'line',            // Line
        'model',           // Model
        'cus_wo',          // Customer work order
        'file_name',       // File name
        'step',            // Step
        'shift',           // Shift
        'download_date',   // Download date
        'submit_date',     // Submit date
        'verify_card_id',  // Verify card ID
        'verify_date',     // Verify date
        'card_id',         // Card ID
        'template_id',     // Template ID
    ];
}
