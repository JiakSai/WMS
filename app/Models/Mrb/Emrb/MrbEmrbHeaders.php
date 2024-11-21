<?php

namespace App\Models\Mrb\Emrb;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MrbEmrbHeaders extends Model
{
    protected $primaryKey = 'mbr_number'; // Specify the primary key column
    public $incrementing = false; // If mbr_number is not an auto-incrementing key
    protected $keyType = 'string'; // Specify if mbr_number is not an integer
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'mbr_number',
        'organisation_id',
        'created_by',
        'updated_by',
        'cost_born_by',
        'transfer_no',
        'date_submit',
        'mes_kitlist',
        'plant',
        'line',
        'model',
        'type',
        'initiator',
        'department',
        'organisation_name',
    ];

    public static function generateMrbNumber()
    {
        $prefix = 'MRBGTR';
        $currentDate = Carbon::now();
        $dateSuffix = $currentDate->format('ym'); // YYMM format

        // Find the highest `mbr_number` for the current date based on created_on field
        $latestEntry = MrbEmrbHeaders::where('mbr_number', 'LIKE', $prefix . $dateSuffix . '%')
            ->whereMonth('created_at', $currentDate->month) // Filter by the same month
            ->whereYear('created_at', $currentDate->year)   // Filter by the same year
            ->orderBy('mbr_number', 'desc')
            ->first();

        if ($latestEntry) {
            // Extract and increment the last number part (last 4 digits of mbr_number)
            $lastNumber = (int)substr($latestEntry->mbr_number, -4); // Ensure you're using 'mbr_number' here
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // Start at 0001 if no entry for the date
            $newNumber = '0001';
        }

        // Combine prefix, date suffix, and new number
        $newMrbNumber = $prefix . $dateSuffix . $newNumber;

        return $newMrbNumber;
    }
}
