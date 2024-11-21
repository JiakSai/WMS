<?php

namespace App\Http\Controllers\Env\Tmsv;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Env\Tmsv\EnvTmsvSinvs;
use Illuminate\Http\Request;
use App\Models\Sys\Orga\SysOrgaCtrls;

class EnvTmsvUpinsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SysOrgaCtrls $organisation, Request $request)
    {
        return view('env.tmsv.upins.index', compact('organisation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SysOrgaCtrls $organisation, Request $request)
    {
        // $fromDate = $request->input('from_date_up');
        // $toDate = $request->input('to_date_up');
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $PN = (string) $request->input('PN', '');

        $query = EnvTmsvSinvs::query();

        if (!empty($PN)) {
            $query->where('PN', 'LIKE', "%{$PN}%");
        }
        if (!empty($fromDate)) {
            $query->whereDate('Complete_Date', '>=', Carbon::parse($fromDate)->startOfDay());
        }
    
        if (!empty($toDate)) {
            $query->whereDate('Complete_Date', '<=', Carbon::parse($toDate)->endOfDay());
        }

        $totalRecords = EnvTmsvSinvs::count();
        $filteredRecords = $query->count();

        $data = 
                $query        
           
                ->orderBy('id', 'DESC')
                    ->offset($start)
                    ->limit($length)
                    ->get();
                        
        $data = $data->map(function ($item) {

            return [
                'DocumentType' => 'Sinvoice',
                'InvoiceID' => 'DummyInvoiceId',
                'DocumentDate' => Carbon::parse($item->DocumentDate)->format('m/d/Y H:i:s'),
                'TIN' => '000840675328',
                'BRN' => '16351-W',
                'CusRegName' => 'WESTERN DIGITAL (MALAYSIA) SDN. BHD.',
                'CusAddress1' => '2580 TINGKAT PERUSAHAAN 4B',
                'CusAddress2' => 'ZON PERDAGANGAN BEBAS DUA',
                'CusAddress3' => 'PENANG, 13600 PERAIMALAYSIA',
                'Country' => 'MYS',
                'City' => 'Perai',
                'StateCode' => '07',
                'Tel' => '03-78705691',
                'Currency' => 'MYR',
                'CurrencyRate' => 1,
                'Terms' => 'MP90',
                'PN'=>$item->PN,
                'Classification' => '022',
                'OrderUOM' => 'pcs',
                'Complete_QTY'=>$item->Complete_QTY,
                'WD_To_JV_Price'=>$item->WD_To_JV_Price,
                'WD_To_JV_Total_Quotation'=>$item->WD_To_JV_Total_Quotation,
                'TaxType' => '06',
                'TaxRate' => 0,
                'TaxAmount' => 0,
                'TaxPrice' => 0,
                'ShipReceiptName' => 'WESTERN DIGITAL (MALAYSIA) SDN. BHD.',
                'ShipAddress1' => '2580 TINGKAT PERUSAHAAN 4B',
                'ShipAddress2' => 'ZON PERDAGANGAN BEBAS DUA',
                'ShipAddress3' => 'PENANG, 13600 PERAIMALAYSIA',
                'ShipCountry' => 'MYS',
                'id' => $item->id,              
                'Complete_Date'=>$item->Complete_Date             
            ];

        }); 

        $json_data = [
            // "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data
        ];

        return response()->json($json_data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
