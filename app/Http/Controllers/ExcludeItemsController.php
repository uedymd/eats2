<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExcludeItemsRequest;
use App\Http\Requests\UpdateExcludeItemsRequest;
use Illuminate\Http\Request;
use App\Models\ExcludeItems;

class ExcludeItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd('exclude');
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
     * @param  \App\Http\Requests\StoreExcludeItemsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreExcludeItemsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExcludeItems  $excludeItems
     * @return \Illuminate\Http\Response
     */
    public function show(ExcludeItems $excludeItems)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExcludeItems  $excludeItems
     * @return \Illuminate\Http\Response
     */
    public function edit(ExcludeItems $excludeItems)
    {
        $items = ExcludeItems::find(1);
        return view('exclude_items/edit', compact('items'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateExcludeItemsRequest  $request
     * @param  \App\Models\ExcludeItems  $excludeItems
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $excludes = ExcludeItems::find(1);
        if (is_null($excludes)) {
            $excludes = new ExcludeItems();
        }
        $excludes->keywords = $request->input('keywords');
        $excludes->save();
        return redirect('setting/exclude_items/edit')->with('success', '保存完了');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExcludeItems  $excludeItems
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExcludeItems $excludeItems)
    {
        //
    }
}
