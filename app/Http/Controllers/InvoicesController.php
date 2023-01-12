<?php

namespace App\Http\Controllers;

use App\Models\invoice_attachments;
use App\Models\invoices;
use App\Models\invoices_details;
use App\Models\sections;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Add_invoice_new;
use App\Exports\InvoicesExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Stmt\Return_;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = invoices::all();
        return view('invoices.invoices', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sections = sections::all();
        return view('invoices.add_invoices', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([]);

        invoices::create([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'due_Date' => $request->due_Date,
            'product' => $request->product,
            'section_id' => $request->section,
            'discount' => $request->discount,
            'rate_vat' => $request->rate_vat,
            'value_vat' => $request->value_vat,
            'total' => $request->total,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
            'user' => Auth::user()->name,
            'total' => $request->total,
            'Amount_Commission' => $request->Amount_Commission,
            'Amount_collection' => $request->Amount_collection

        ]);

        $id_invoice = invoices::latest()->first()->id;
        invoices_details::create([
            'invoice_number' => $request->invoice_number,
            'id_Invoice' => $id_invoice,
            'product' => $request->product,
            'section' => $request->section,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name)
        ]);


        if ($request->hasFile('pic')) {

            $invoice_id = Invoices::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $attachments = new invoice_attachments();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->created_by = Auth::user()->name;
            $attachments->invoice_id = $invoice_id;
            $attachments->save();

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);
        }


        // notification

        $user = User::first();
        Notification::send($user, new Add_invoice_new($id_invoice));




        session()->flash('Add', 'تم إضافة الفاتورة بنجاح');
        return redirect('invoices.invoices');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function show(invoices $invoices)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoices = invoices::where('id', $id)->first();
        $sections = sections::all();
        return view('invoices.edit_invoice', compact('sections', 'invoices'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $id = $request->invoices_id;
        $invoices = invoices::find($id);
        $invoices->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'due_Date' => $request->due_Date,
            'product' => $request->product,
            'section_id' => $request->section,
            'discount' => $request->discount,
            'rate_vat' => $request->rate_vat,
            'value_vat' => $request->value_vat,
            'total' => $request->total,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
            'user' => Auth::user()->name,
            'total' => $request->total,
            'Amount_Commission' => $request->Amount_Commission,
            'Amount_collection' => $request->Amount_collection
        ]);

        session()->flash('Edit', 'تم تعديل الفاتورة بنجاح');
        return redirect('invoices.invoices');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoices = invoices::findOrFail($id);
        $invoices->delete();

        session()->flash('Delete', 'تم حذف الفاتورة بنجاح');
        return redirect('invoices.invoices');
    }


    public function status_show($id)
    {

        $invoices = invoices::where('id', $id)->first();
        return view('invoices.status_show', compact('invoices'));
    }

    public function status_update(Request $request, $id)
    {
        $invoices = invoices::findOrFail($id);

        if ($request->status === 'مدفوعة') {

            $invoices->update([
                'value_status' => 1,
                'status' => $request->status,
                'payment_Date' => $request->payment_Date,
            ]);



            invoices_details::create([
                'id_Invoice' => $request->invoices_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'section' => $request->section,
                'status' => $request->status,
                'value_status' => 1,
                'note' => $request->note,
                'payment_Date' => $request->payment_Date,
                'user' => (Auth::user()->name),
            ]);
        } else {

            $invoices->update([

                'value_status' => 3,
                'status' => $request->status,
                'payment_Date' => $request->payment_Date,
            ]);

            invoices_details::create([
                'id_Invoice' => $request->invoices_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'section' => $request->section,
                'status' => $request->status,
                'value_status' => 3,
                'note' => $request->note,
                'payment_Date' => $request->payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }
        session()->flash('Edit', 'تم تعديل حالة دفع الفاتورة بنجاح');
        return redirect('invoices.invoices');
    }



    public function paid_invoices()
    {
        $invoices = invoices::where('value_status', 1)->get();
        return view('invoices.paid_invoices', compact('invoices'));
    }

    public function un_paid_invoices()
    {
        $invoices = invoices::where('value_status', 2)->get();
        return view('invoices.un_paid_invoices', compact('invoices'));
    }

    public function paid_partial_invoices()
    {
        $invoices = invoices::where('value_status', 3)->get();
        return view('invoices.paid_partial_invoices', compact('invoices'));
    }

    public function getproducts($id)
    {
        $products = DB::table("products")->where("section_id", $id)->pluck("Product_name", "id");
        return json_encode($products);
    }

    public function export()
    {
        return Excel::download(new InvoicesExport, 'قائمة الفواتير.xlsx');
    }
}
