<?php

namespace App\Http\Controllers;

use App\Models\RateSet;
use Illuminate\Http\Request;

class RateSetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rate_sets = RateSet::all();
        return view('rateset/index', compact('rate_sets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('rateset/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $price_mins = $request->input('price_min');
        $price_maxs = $request->input('price_max');
        $price_rates = $request->input('price_rate');
        $setting = [];

        $i = 0;

        while ((!empty($price_mins[$i]) || !empty($price_maxs[$i]) && !empty($price_rates[$i]))) {
            $setting[] = [
                'min'   => $price_mins[$i],
                'max'   => $price_maxs[$i],
                'rate'   => $price_rates[$i],
            ];
            $i++;
        }

        $rate_set = new RateSet();
        $rate_set->name = $request->input('name');
        $rate_set->set = serialize($setting);
        $rate_set->save();

        return redirect('setting/rateset');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RateSet  $rateSet
     * @return \Illuminate\Http\Response
     */
    public function show(RateSet $rateSet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RateSet  $rateSet
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, RateSet $rateSet)
    {
        $rate_set = RateSet::find($request->id);
        return view('rateset/edit', compact('rate_set'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RateSet  $rateSet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RateSet $rateSet)
    {
        $price_mins = $request->input('price_min');
        $price_maxs = $request->input('price_max');
        $price_rates = $request->input('price_rate');
        $setting = [];

        $i = 0;

        while ((!empty($price_mins[$i]) || !empty($price_maxs[$i]) && !empty($price_rates[$i]))) {
            $setting[] = [
                'min'   => $price_mins[$i],
                'max'   => $price_maxs[$i],
                'rate'   => $price_rates[$i],
            ];
            $i++;
        }

        $rate_set = RateSet::find($request->id);
        $rate_set->name = $request->input('name');
        $rate_set->set = serialize($setting);
        $rate_set->update();
        return redirect('setting/rateset')->with('success', '保存完了');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RateSet  $rateSet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, RateSet $rateSet)
    {
        $rate_set = RateSet::find($request->id);

        if ($rate_set->count() > 0) {
            $rate_set->delete();
        }
        return redirect('setting/rateset');
    }
}
