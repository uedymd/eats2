<?php

namespace App\Http\Controllers\Rakuten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rakuten;
use App\Models\RakutenItem;
use App\Models\BrandSet;
use Illuminate\Support\Facades\App;

class RakutenController extends Controller
{

    public $status_array = [
        1 => "稼働",
        2 => "停止",
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rakutens = Rakuten::all();
        $items = [];
        foreach ($rakutens as $rakuten) {
            $count = RakutenItem::where('rakuten_id', $rakuten->id)->count();
            $items[$rakuten->id] = $count;
        }
        $status_array = $this->status_array;

        return view('rakuten/index', compact('rakutens', 'items', 'status_array'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $brand_sets = BrandSet::select('id', 'name')->get();
        $selector = ['' => 'ブランドセットを選択'];

        foreach ($brand_sets as $brand_set) {
            $selector[$brand_set->id] = $brand_set->name;
        }

        return view('rakuten/create', compact('selector'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rakuten = new Rakuten();
        $rakuten->title = $request->input('title');
        $rakuten->keyword = $request->input('keyword');
        $rakuten->genre = $request->input('genre');
        $rakuten->genre_id = $request->input('genre_id');
        $rakuten->ng_keyword = $request->input('ng_keyword');
        $rakuten->ng_url = $request->input('ng_url');
        $rakuten->ng_url = $request->input('ng_url');
        $rakuten->price_min = $request->input('price_min');
        $rakuten->price_max = $request->input('price_max');
        $rakuten->rate = $request->input('rate');
        $rakuten->status = 1;
        $rakuten->save();
        return redirect('api/rakuten/search');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rakuten = Rakuten::find($id);
        $status_array = $this->status_array;

        $brand_sets = BrandSet::select('id', 'name')->get();
        $selector = ['' => 'ブランドセットを選択'];

        foreach ($brand_sets as $brand_set) {
            $selector[$brand_set->id] = $brand_set->name;
        }
        return view('rakuten/edit', compact('rakuten', 'status_array', 'selector'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rakuten = Rakuten::find($id);
        $rakuten->title = $request->input('title');
        $rakuten->keyword = $request->input('keyword');
        $rakuten->genre = $request->input('genre');
        $rakuten->genre_id = $request->input('genre_id');
        $rakuten->ng_keyword = $request->input('ng_keyword');
        $rakuten->ng_url = $request->input('ng_url');
        $rakuten->ng_url = $request->input('ng_url');
        $rakuten->price_min = $request->input('price_min');
        $rakuten->price_max = $request->input('price_max');
        $rakuten->rate = $request->input('rate');
        $rakuten->status = $request->input('status');
        $rakuten->save();
        return redirect('api/rakuten/search');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $rakuten = Rakuten::find($id);
        return view('rakuten/delete', compact('rakuten'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rakuten = Rakuten::find($id);
        if ($rakuten->delete()) {
            $result = RakutenItem::where('rakuten_id', $id)->delete();
        }
        return redirect('rakuten');
    }
}
