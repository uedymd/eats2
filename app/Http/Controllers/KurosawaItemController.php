<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKurosawaItemRequest;
use App\Http\Requests\UpdateKurosawaItemRequest;
use App\Models\KurosawaItem;

class KurosawaItemController extends Controller
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
     * @param  \App\Http\Requests\StoreKurosawaItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreKurosawaItemRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\KurosawaItem  $kurosawaItem
     * @return \Illuminate\Http\Response
     */
    public function show(KurosawaItem $kurosawaItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\KurosawaItem  $kurosawaItem
     * @return \Illuminate\Http\Response
     */
    public function edit(KurosawaItem $kurosawaItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateKurosawaItemRequest  $request
     * @param  \App\Models\KurosawaItem  $kurosawaItem
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateKurosawaItemRequest $request, KurosawaItem $kurosawaItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\KurosawaItem  $kurosawaItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(KurosawaItem $kurosawaItem)
    {
        //
    }
}
