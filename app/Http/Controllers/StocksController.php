<?php

namespace App\Http\Controllers;

use App\Models\Stocks;
use App\Models\RakutenItem;
use App\Models\DigimartItems;
use Illuminate\Http\Request;

class StocksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($site)
    {
        $models = [
            'rakuten' => 'App\Models\RakutenItem',
            'digimart' => 'App\Models\DigimartItems',
        ];

        $items = $models[$site]::where('en_title', '!=', '')
            ->where('en_content', '!=', '')
            ->where('en_brand', '!=', '')
            ->leftJoin("{$site}s", "{$site}s.id", '=', "{$site}_items.{$site}_id")
            ->select("{$site}_items.id", "{$site}_items.en_title", "{$site}_items.en_content")
            ->where("{$site}s.status", '=', 3)
            ->get();



        foreach ($items as $item) {
            $stock_count = Stocks::where('item_id', '=', $item->id)
                ->where('site', $site)->count();
            if ($stock_count == 0) {
                $stocks = new Stocks();
                $stocks->item_id = $item->id;
                $stocks->site = $site;
                $stocks->status = 1;
                $result = $stocks->save();
            }
        }
        return $result;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stocks = Stocks::all();
        return view('stock/index', compact('stocks'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stocks  $stocks
     * @return \Illuminate\Http\Response
     */
    public function show(Stocks $stocks)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Stocks  $stocks
     * @return \Illuminate\Http\Response
     */
    public function edit(Stocks $stocks)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stocks  $stocks
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stocks $stocks)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stocks  $stocks
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stocks $stocks)
    {
        //
    }
}
