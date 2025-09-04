<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FinancialRecordsImport;
use App\Imports\OfficeRevenuesImport;
use App\Imports\TripsPassengersImport;

class ImportController extends Controller
{
    public function showForm()
    {
        return view('import.form');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls']);
        $type = $request->input('type');
        $file = $request->file('file');

        switch ($type) {
            case 'financial_records':
                Excel::import(new FinancialRecordsImport, $file);
                break;
            case 'office_revenues':
                Excel::import(new OfficeRevenuesImport, $file);
                break;
            case 'trips_passengers':
                Excel::import(new TripsPassengersImport, $file);
                break;
            default:
                return back()->with('error', 'Loại dữ liệu không hợp lệ.');
        }

        return back()->with('success', 'Nhập dữ liệu thành công!');
    }
}
