<?php

namespace App\Http\Controllers;

use App\Models\templates;
use Illuminate\Http\Request;

class TemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = templates::all();
        return view('template/index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('template/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $templates = new templates();
        $templates->title = $request->input('title');
        $templates->source = $request->input('source');
        $templates->save();

        return redirect('setting/template');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\templates  $templates
     * @return \Illuminate\Http\Response
     */
    public function show(templates $templates)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\templates  $templates
     * @return \Illuminate\Http\Response
     */
    public function edit(templates $templates, $id)
    {
        $templates = templates::find($id);
        return view('template/edit', compact('templates'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\templates  $templates
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, templates $templates, $id)
    {
        $templates = templates::find($id);
        $templates->title = $request->input('title');
        $templates->source = $request->input('source');
        $templates->update();
        return redirect('setting/template');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\templates  $templates
     * @return \Illuminate\Http\Response
     */
    public function destroy(templates $templates, $id)
    {
        $templates = templates::find($id);

        if ($templates->count() > 0) {
            $templates->delete();
        }
        return redirect('setting/template');
    }
}
