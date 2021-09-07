<?php

namespace App\Http\Controllers;

use App\Models\BrandSet;
use Illuminate\Http\Request;

class BrandSetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brand_sets = BrandSet::all();
        return view('brandset/index', compact('brand_sets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('brandset/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'set' => 'required',
        ], [
            'name.required' => '設定名は必須です',
            'set.required' => 'ブランドは必須です',
        ]);
        $brand_set = new BrandSet();
        $brand_set->insert([
            'name'   => $request->input('name'),
            'set'   => $request->input('set'),
        ]);

        return redirect('setting/brandset')->with('success', '保存完了');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BrandSet  $brandSet
     * @return \Illuminate\Http\Response
     */
    public function show(BrandSet $brandSet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BrandSet  $brandSet
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, BrandSet $brandSet)
    {
        $brand_set = BrandSet::find($request->id);
        return view('brandset/edit', compact('brand_set'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BrandSet  $brandSet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BrandSet $brandSet)
    {
        $brand_set = BrandSet::find($request->id);
        $brand_set->name = $request->input('name');
        $brand_set->set = $request->input('set');
        $brand_set->timestamps = false;
        $brand_set->update();
        return redirect('setting/brandset')->with('success', '保存完了');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BrandSet  $brandSet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, BrandSet $brandSet)
    {
        $brand_set = BrandSet::find($request->id);
        $brand_set->delete();
        return redirect('setting/brandset');
    }
}
