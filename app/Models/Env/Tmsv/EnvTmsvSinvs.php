<?php

namespace App\Models\Env\Tmsv;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnvTmsvSinvs extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'PN',
        'WO',
        'PO_NO',
        'QTY',
        'WD_To_JV_Price',
        'WD_To_JV_Total_Quotation',
        'TransactionNo',
        'Complete_QTY',
        'Complete_Date',
        'Location',
        'JV_To_SMTT_Price',
        'JV_To_SMTT_Total_Quotation',
        'organisation_id',
        'created_by',
        'updated_by',
        'SumTotalQuotation',
        'FileName',
   
    ];

   
    protected $dates = [
        'Complete_Date', 
    ];
 

}
