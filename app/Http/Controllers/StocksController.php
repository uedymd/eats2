<?php

namespace App\Http\Controllers;

use App\Models\Stocks;
use App\Models\RakutenItem;
use Illuminate\Http\Request;

class StocksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        $items = RakutenItem::where('en_title', '!=', '')
            ->where('en_content', '!=', '')
            ->where('en_brand', '!=', '')
            ->leftJoin('rakutens', 'rakutens.id', '=', 'rakuten_items.rakuten_id')
            ->select('rakuten_items.id', 'rakuten_items.en_title', 'rakuten_items.en_content')
            ->where('rakutens.status', '=', 3)
            ->get();

        dd($items);

        foreach ($items as $item) {
            $stock_count = Stocks::where('item_id', '=', $item->id)
                ->where('site', 'rakuten')->count();
            var_dump($stock_count);
            if ($stock_count == 0) {
                $stocks = new Stocks();
                $stocks->item_id = $item->id;
                $stocks->site = 'rakuten';
                $stocks->status = 1;
                $stocks->save();
            }
        }
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
