<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKurosawaRequest;
use App\Http\Requests\UpdateKurosawaRequest;
use App\Models\Kurosawa;

class KurosawaController extends Controller
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
     * @param  \App\Http\Requests\StoreKurosawaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreKurosawaRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Kurosawa  $kurosawa
     * @return \Illuminate\Http\Response
     */
    public function show(Kurosawa $kurosawa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Kurosawa  $kurosawa
     * @return \Illuminate\Http\Response
     */
    public function edit(Kurosawa $kurosawa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateKurosawaRequest  $request
     * @param  \App\Models\Kurosawa  $kurosawa
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateKurosawaRequest $request, Kurosawa $kurosawa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Kurosawa  $kurosawa
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kurosawa $kurosawa)
    {
        //
    }
}
