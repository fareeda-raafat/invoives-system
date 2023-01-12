<?php

namespace App\Http\Controllers;

use App\Models\sections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SectionRequest;

class SectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sections = sections::all();
        return  view('sections.sections', compact('sections'));
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
     * @param  \Illuminate\Http\SectionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SectionRequest $request)
    {

        $validatedData = $request->validated();

        sections::create([

            'section_name' => $request->section_name,
            'description' => $request->description,
            'Created_by' => (Auth::user()->name)

        ]);

        session()->flash('Add', 'تم إضافة القسم بنجاح');
        return redirect('/sections');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\sections  $sections
     * @return \Illuminate\Http\Response
     */
    public function show(sections $sections)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\sections  $sections
     * @return \Illuminate\Http\Response
     */
    public function edit(sections $sections)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\sections  $sections
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request->id;

        $request->validate(
            [
                'section_name' => 'required|max:250|unique:sections,section_name,' . $id
            ],
            [
                'section_name.required' => 'اسم القسم مطلوب'
            ]
        );

        $section = sections::find($id);
        $section->update([
            'section_name' => $request->section_name,
            'description' => $request->description
        ]);

        session()->flash('Edit', 'تم تعديل القسم بنجاح');
        return redirect('/sections');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\sections  $sections
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        echo 'delete';
        $id = $request->id;
        sections::find($id)->delete();
        session()->flash('Delete', 'تم حذف القسم بنجاح');
        return redirect('/sections');
    }
}
