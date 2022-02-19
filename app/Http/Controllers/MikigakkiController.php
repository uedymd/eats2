<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mikigakki;
use App\Models\MikigakkiItem;
use App\Models\BrandSet;
use App\Models\RateSet;
use App\Models\templates;
use Illuminate\Support\Facades\App;

class MikigakkiController extends Controller
{

    public $status_array = [
        1 => "保留",
        3 => "稼働",
        2 => "停止",
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mikigakkis = Mikigakki::leftJoin('brand_sets', 'mikigakkis.brand_set_id', '=', 'brand_sets.id')
            ->leftJoin('rate_sets', 'mikigakkis.rate_set_id', '=', 'rate_sets.id')
            ->select('mikigakkis.id as id', 'title', 'url', 'ng_keyword', 'brand_set_id', 'rate_set_id', 'ng_url',  'status', 'mikigakkis.checked_at', 'brand_sets.name as brand_set_name', 'rate_sets.name as rate_set_name', 'priority')
            ->orderBy('priority')
            ->get();
        $items = [];
        foreach ($mikigakkis as $mikigakki) {
            $count = MikigakkiItem::where('mikigakki_id', $mikigakki->id)->count();
            $count_jp_content_null = MikigakkiItem::where('mikigakki_id', $mikigakki->id)
                ->whereNull('jp_content')
                ->count();
            $count_en_title_null = MikigakkiItem::where('mikigakki_id', $mikigakki->id)
                ->whereNull('en_title')
                ->count();
            $count_en_brand_null = MikigakkiItem::where('mikigakki_id', $mikigakki->id)
                ->whereNull('en_brand')
                ->count();
            $count_en_content_null = MikigakkiItem::where('mikigakki_id', $mikigakki->id)
                ->whereNull('en_content')
                ->count();
            $count_doller_null = MikigakkiItem::where('mikigakki_id', $mikigakki->id)
                ->whereNull('doller')
                ->count();
            $items[$mikigakki->id] = [
                'count' => $count,
                'jp_content' => $count_jp_content_null,
                'en_content' => $count_en_content_null,
                'en_title' => $count_en_title_null,
                'en_brand' => $count_en_brand_null,
                'doller' => $count_doller_null,
            ];
        }



        $status_array = $this->status_array;

        return view('mikigakki/index', compact('mikigakkis', 'items', 'status_array'));
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

        $rate_sets = RateSet::select('id', 'name')->get();
        $rate_sets = RateSet::select('id', 'name')->get();
        $rate_selector = ['' => '金額レートを選択'];


        foreach ($rate_sets as $rate_set) {
            $rate_selector[$rate_set->id] = $rate_set->name;
        }

        $template_selector = ['' => 'テンプレートを選択'];
        $templates = templates::select('id', 'title')->get();
        foreach ($templates as $template) {
            $template_selector[$template->id] = $template->title;
        }

        return view('mikigakki/create', compact('selector', 'rate_selector', 'template_selector'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $mikigakki = new Mikigakki();
        $mikigakki->title = $request->input('title');
        $mikigakki->url = $request->input('url');
        $mikigakki->ebay_category = $request->input('ebay_category');
        $mikigakki->ng_keyword = $request->input('ng_keyword');
        $mikigakki->brand_set_id = $request->input('brand_set_id');
        $mikigakki->rate_set_id = $request->input('rate_set_id');
        $mikigakki->ng_url = $request->input('ng_url');
        $mikigakki->best_offer = $request->input('best_offer');
        $mikigakki->sku = $request->input('sku');
        $mikigakki->type = $request->input('type');
        $mikigakki->payment_profile = $request->input('payment_profile');
        $mikigakki->return_profile = $request->input('return_profile');
        $mikigakki->shipping_profile = $request->input('shipping_profile');
        $mikigakki->condition = $request->input('condition');
        $mikigakki->template = $request->input('template');
        $mikigakki->priority = $request->input('priority');
        $mikigakki->status = 1;
        $mikigakki->save();
        return redirect('mikigakki');
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
        $mikigakki = Mikigakki::find($id);
        $status_array = $this->status_array;

        $brand_sets = BrandSet::select('id', 'name')->get();
        $selector = ['' => 'ブランドセットを選択'];

        foreach ($brand_sets as $brand_set) {
            $selector[$brand_set->id] = $brand_set->name;
        }

        $rate_sets = RateSet::select('id', 'name')->get();
        $rate_sets = RateSet::select('id', 'name')->get();
        $rate_selector = ['' => '金額レートを選択'];

        foreach ($rate_sets as $rate_set) {
            $rate_selector[$rate_set->id] = $rate_set->name;
        }

        $template_selector = ['' => 'テンプレートを選択'];
        $templates = templates::select('id', 'title')->get();
        foreach ($templates as $template) {
            $template_selector[$template->id] = $template->title;
        }

        return view('mikigakki/edit', compact('mikigakki', 'status_array', 'selector', 'rate_selector', 'template_selector'));
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
        $mikigakki = Mikigakki::find($id);
        $mikigakki->title = $request->input('title');
        $mikigakki->url = $request->input('url');
        $mikigakki->ebay_category = $request->input('ebay_category');
        $mikigakki->ng_keyword = $request->input('ng_keyword');
        $mikigakki->brand_set_id = $request->input('brand_set_id');
        $mikigakki->rate_set_id = $request->input('rate_set_id');
        $mikigakki->ng_url = $request->input('ng_url');
        $mikigakki->best_offer = $request->input('best_offer');
        $mikigakki->sku = $request->input('sku');
        $mikigakki->type = $request->input('type');
        $mikigakki->payment_profile = $request->input('payment_profile');
        $mikigakki->return_profile = $request->input('return_profile');
        $mikigakki->shipping_profile = $request->input('shipping_profile');
        $mikigakki->condition = $request->input('condition');
        $mikigakki->template = $request->input('template');
        $mikigakki->priority = $request->input('priority');
        $mikigakki->status = $request->input('status');
        $mikigakki->save();
        return redirect('mikigakki');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $mikigakki = Mikigakki::find($id);

        return view('mikigakki/delete', compact('mikigakki'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if (MikigakkiItem::where('mikigakki_id', $id)->count() > 0) {
            MikigakkiItem::where('mikigakki_id', $id)->delete();
        }
        Mikigakki::find($id)->delete();
        return redirect('mikigakki');
    }
}
