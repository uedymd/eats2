<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMikigakkiItemRequest;
use App\Http\Requests\UpdateMikigakkiItemRequest;
use App\Models\MikigakkiItem;

class MikigakkiItemController extends Controller
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
     * @param  \App\Http\Requests\StoreMikigakkiItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMikigakkiItemRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MikigakkiItem  $mikigakkiItem
     * @return \Illuminate\Http\Response
     */
    public function show(MikigakkiItem $mikigakkiItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MikigakkiItem  $mikigakkiItem
     * @return \Illuminate\Http\Response
     */
    public function edit(MikigakkiItem $mikigakkiItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMikigakkiItemRequest  $request
     * @param  \App\Models\MikigakkiItem  $mikigakkiItem
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMikigakkiItemRequest $request, MikigakkiItem $mikigakkiItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MikigakkiItem  $mikigakkiItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(MikigakkiItem $mikigakkiItem)
    {
        //
    }
}
