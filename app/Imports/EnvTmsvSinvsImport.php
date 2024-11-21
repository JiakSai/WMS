<?php

namespace App\Imports;

use App\Models\Env\Tmsv\EnvTmsvSinvs; // Model to import data into
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // For heading rows (if your Excel file has them)
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\AfterImport; 
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Env\Tmsv\EnvTmsvImportSummary;


class EnvTmsvSinvsImport implements ToModel, WithHeadingRow
{
    protected $fileName;
    protected $totalQuotationSum = 0;
    protected $organisation;
    protected $createdBy;
    
    
    public function __construct( $fileName,$organisation, $createdBy)
    {
        $this->fileName = $fileName;
        $this->organisation = $organisation;
        $this->createdBy = $createdBy;
    }
    public function model(array $row)
    {
        try {
        if (empty($row['wd_pn'])) {
            Log::info('Skipping row as wd_pn is empty', ['row' => $row]);
            return null; // Skip the row
        }
        // Log the row for debugging
        // Log::debug('Imported row: ', $row);
        
        $fileName = $this->fileName;


        // Set the file path dynamically using the file name
        $filePath = storage_path('app/uploads/' . $fileName); // Adjust to your actual file path
        
        // Check if the file exists before loading it
        if (!file_exists($filePath)) {
            Log::error('File does not exist at path: ' . $filePath);
            throw new \Exception('File does not exist at the specified path: ' . $filePath);
        }

        // Load the spreadsheet dynamically
        // $spreadsheet = IOFactory::load($filePath);
        // $sheet = $spreadsheet->getActiveSheet();
        // // Retrieve the calculated values from the cells directly
        $totalQuotation =  $row['complete_qty'] * $row['wd_to_jv_price'];

        $this->totalQuotationSum += $totalQuotation;
        $completeDate = $row['complete_date'];
        if (is_numeric($completeDate)) {
            $completeDate = Carbon::instance(Date::excelToDateTimeObject($completeDate));
        } else {
            // Handle non-numeric or invalid dates
            $completeDate = Carbon::now(); // Or any default value you prefer
        }
        return new EnvTmsvSinvs([
            'PN' => $row['wd_pn'],
            'WO' => $row['wd_wo'],
            'PO_NO' => $row['wd_po_no'],
            'QTY' => $row['po_qty'],
            'WD_To_JV_Price' => $row['wd_to_jv_price'],
            'WD_To_JV_Total_Quotation' => $totalQuotation,  // Using calculated value
            'TransactionNo' => $row['transaction_number'],
            'Complete_QTY' => $row['complete_qty'],
            'Complete_Date' => $completeDate,
            'Location' => $row['location'],
            'JV_To_SMTT_Price' => $row['jv_to_smtt_price'],
            'organisation_id' => $this->organisation->id,
            'FileName' => $fileName ,
            'created_by' => $this->createdBy
            

            // 'SumTotalQuotation' => $this->totalQuotationSum, 
        ]);
    } catch (\Exception $e) {
        // Log the exception message
        Log::error('Error importing row: ' . $e->getMessage(), [
            'file' => $this->fileName,
            'row' => $row,
            'exception' => $e
        ]);
        // Optionally, you can also rethrow the exception or handle it as needed
        throw $e;
    }
}


}