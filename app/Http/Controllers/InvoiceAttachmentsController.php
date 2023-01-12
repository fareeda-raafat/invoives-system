<?php

namespace App\Http\Controllers;

use App\Models\invoice_attachments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceAttachmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(
            $request,
            [

                'invoice_file' =>  'mimes:pdf,jpeg,png,jpg'
            ],
            [
                'invoice_file.mimes' => 'صيغة المرفق يجب ان تكون   pdf, jpeg , png , jpg',
            ],
        );

        $invoice_id = $request->invoice_id;
        $invoice_number = $request->invoice_num;
        $img = $request->file('invoice_file');
        $img_name = $img->getClientOriginalName();
        $created_by = Auth::user()->name;

        $invoice_attachments = new invoice_attachments();
        $invoice_attachments->invoice_id = $invoice_id;
        $invoice_attachments->invoice_number = $invoice_number;
        $invoice_attachments->file_name = $img_name;
        $invoice_attachments->created_by = $created_by;
        $invoice_attachments->save();

        $invoice_img = $request->invoice_file->getClientOriginalName();
        $request->invoice_file->move(public_path('Attachments/' . $invoice_number), $invoice_img);



        session()->flash('Add', 'تم اضافة المرفق بنجاح');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\invoice_attachments  $invoice_attachments
     * @return \Illuminate\Http\Response
     */
    public function show(invoice_attachments $invoice_attachments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\invoice_attachments  $invoice_attachments
     * @return \Illuminate\Http\Response
     */
    public function edit(invoice_attachments $invoice_attachments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\invoice_attachments  $invoice_attachments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\invoice_attachments  $invoice_attachments
     * @return \Illuminate\Http\Response
     */
    public function destroy(invoice_attachments $invoice_attachments)
    {
        //
    }
}
