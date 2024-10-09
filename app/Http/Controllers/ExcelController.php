<?php

namespace App\Http\Controllers;

use App\Models\Excel;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Writer\Html as HtmlWriter;
use PhpOffice\PhpSpreadsheet\Reader\Html as HtmlReader;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExcelController extends Controller
{
    public function index(Warehouse $warehouse){
        return view('excelControl', compact('warehouse'));
    }

    public function getExcelData(Warehouse $warehouse, Request $request){

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $name = $request->input('name', '');

        $query = Excel::query();

        if (!empty($name)) {
            $query->where('name', 'LIKE', "%{$name}%");
        }

        $totalRecords = Excel::count();
        $filteredRecords = $query->count();

        $excel = $query->where('is_deleted', 0)
                        ->orderBy('id', 'DESC')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                       
        $excel = $excel->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'created_by' => $item->created_by,
                    'updated_by' => $item->updated_by,
                ];
            }); 

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $excel
        ];

        return response()->json($json_data);
    }

    public function addExcel(Warehouse $warehouse, Request $request){
        
        $data = view('addExcel', compact('warehouse'))->render();

        return response()->json(['data' => $data]);
    }

    public function editExcel(Warehouse $warehouse, Request $request)
    {
        $spreadsheet = Excel::findOrFail($request->id);
        $filePath = storage_path('app/' . $spreadsheet->path);
        
        $spreadsheetObj = IOFactory::load($filePath);
        $writer = new Html($spreadsheetObj);
        
        $sheets = [];
        foreach ($spreadsheetObj->getWorksheetIterator() as $worksheet) {
            $sheetIndex = $spreadsheetObj->getIndex($worksheet);
            $writer->setSheetIndex($sheetIndex);
            $sheets[] = [
                'name' => $worksheet->getTitle(),
                'html' => $writer->generateHtmlAll()
            ];
        }

        $data = view('editExcel', compact('spreadsheet', 'sheets'))->render();

        return response()->json(['data' => $data]);
    }

    public function addExcelSubmit(Warehouse $warehouse, Request $request){

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        $path = $file->store('public/excel');

        Excel::create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'warehouse_id' => $warehouse->id,
            'created_by' => Auth::user()->name,
            'updated_by'=> Auth::user()->name,
        ]);

        return response()->json(['status' => 2, 'message' => 'Module added successfully']);
    }

    public function editExcelSubmit(Request $request, $id)
    {
        $spreadsheet = Excel::findOrFail($id);
        $filePath = storage_path('app/' . $spreadsheet->path);

        $spreadsheetObj = IOFactory::load($filePath);
        $sheetsData = $request->input('sheets');

        foreach ($sheetsData as $sheetName => $htmlContent) {
            $sheet = $spreadsheetObj->getSheetByName($sheetName) ?? $spreadsheetObj->createSheet();
            $sheet->setTitle($sheetName);

            $reader = new HtmlReader();
            $tempSpreadsheet = $reader->loadFromString($htmlContent);
            $tempSheet = $tempSpreadsheet->getActiveSheet();

            $sheet->fromArray($tempSheet->toArray(null, true, true, true));
        }

        $writer = IOFactory::createWriter($spreadsheetObj, 'Xlsx');
        $writer->save($filePath);

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $spreadsheet = Excel::findOrFail($id);
        $filePath = storage_path('app/' . $spreadsheet->path);

        $spreadsheetObj = IOFactory::load($filePath);
        $sheets = [];

        foreach ($spreadsheetObj->getWorksheetIterator() as $worksheet) {
            $sheetName = $worksheet->getTitle();
            $writer = new HtmlWriter($spreadsheetObj);
            $writer->setSheetIndex($spreadsheetObj->getIndex($worksheet));
            $sheets[$sheetName] = $writer->generateSheetData();
        }

        return view('edit', compact('spreadsheet', 'sheets'));
    }
}