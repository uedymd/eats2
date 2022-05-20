<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExcludeItemsRequest;
use App\Http\Requests\UpdateExcludeItemsRequest;
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateExcludeItemsRequest  $request
     * @param  \App\Models\ExcludeItems  $excludeItems
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExcludeItemsRequest $request, ExcludeItems $excludeItems)
    {
        //
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
