<?php

namespace App\Http\Controllers;

use App\Models\invoices;
use Illuminate\Http\Request;

class InvoicsReportController extends Controller
{
    public function index()
    {

        return view('reports.invoices_reports');
    }

    public function Search_invoices(Request $request)
    {
        $rdio = $request->rdio;
        if ($rdio == 1) {

            $type = $request->type;
            $start_at = date($request->start_at);
            $end_at = date($request->end_at);


            if ($request->type && $request->start_at == '' && $request->end_at == '') {
                $invoices = invoices::select('*')->where('status', $request->type)->get();
                return view('reports.invoices_reports', compact('invoices', 'end_at', 'start_at', 'type'));
            } else {
                $invoices = invoices::whereBetween('invoice_Date', [$start_at, $end_at])->where('status', '=', $type)->get();
                return view('reports.invoices_reports', compact('invoices', 'end_at', 'start_at', 'type'));
            }
        } else {
            $invoices = invoices::select('*')->where('invoice_number', $request->invoice_number)->get();
            return view('reports.invoices_reports', compact('invoices'));
        }
    }
}
