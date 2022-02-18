<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMikigakkiRequest;
use App\Http\Requests\UpdateMikigakkiRequest;
use App\Models\Mikigakki;

class MikigakkiController extends Controller
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
     * @param  \App\Http\Requests\StoreMikigakkiRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMikigakkiRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Mikigakki  $mikigakki
     * @return \Illuminate\Http\Response
     */
    public function show(Mikigakki $mikigakki)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Mikigakki  $mikigakki
     * @return \Illuminate\Http\Response
     */
    public function edit(Mikigakki $mikigakki)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMikigakkiRequest  $request
     * @param  \App\Models\Mikigakki  $mikigakki
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMikigakkiRequest $request, Mikigakki $mikigakki)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Mikigakki  $mikigakki
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mikigakki $mikigakki)
    {
        //
    }
}
