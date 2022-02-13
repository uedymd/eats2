<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kurosawa;
use App\Models\KurosawaItem;
use App\Models\BrandSet;
use App\Models\RateSet;
use App\Models\templates;
use Illuminate\Support\Facades\App;

class KurosawaController extends Controller
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
        $kurosawas = Kurosawa::leftJoin('brand_sets', 'kurosawas.brand_set_id', '=', 'brand_sets.id')
            ->leftJoin('rate_sets', 'kurosawas.rate_set_id', '=', 'rate_sets.id')
            ->select('kurosawas.id as id', 'title', 'url', 'ng_keyword', 'brand_set_id', 'rate_set_id', 'ng_url',  'status', 'kurosawas.checked_at', 'brand_sets.name as brand_set_name', 'rate_sets.name as rate_set_name', 'priority')
            ->orderBy('priority')
            ->get();
        $items = [];
        foreach ($kurosawas as $kurosawa) {
            $count = KurosawaItem::where('kurosawa_id', $kurosawa->id)->count();
            $count_jp_content_null = KurosawaItem::where('kurosawa_id', $kurosawa->id)
                ->whereNull('jp_content')
                ->count();
            $count_en_title_null = KurosawaItem::where('kurosawa_id', $kurosawa->id)
                ->whereNull('en_title')
                ->count();
            $count_en_brand_null = KurosawaItem::where('kurosawa_id', $kurosawa->id)
                ->whereNull('en_brand')
                ->count();
            $count_en_content_null = KurosawaItem::where('kurosawa_id', $kurosawa->id)
                ->whereNull('en_content')
                ->count();
            $count_doller_null = KurosawaItem::where('kurosawa_id', $kurosawa->id)
                ->whereNull('doller')
                ->count();
            $items[$kurosawa->id] = [
                'count' => $count,
                'jp_content' => $count_jp_content_null,
                'en_content' => $count_en_content_null,
                'en_title' => $count_en_title_null,
                'en_brand' => $count_en_brand_null,
                'doller' => $count_doller_null,
            ];
        }



        $status_array = $this->status_array;

        return view('kurosawa/index', compact('kurosawas', 'items', 'status_array'));
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

        return view('kurosawa/create', compact('selector', 'rate_selector', 'template_selector'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $kurosawa = new kurosawa();
        $kurosawa->title = $request->input('title');
        $kurosawa->url = $request->input('url');
        $kurosawa->ebay_category = $request->input('ebay_category');
        $kurosawa->ng_keyword = $request->input('ng_keyword');
        $kurosawa->brand_set_id = $request->input('brand_set_id');
        $kurosawa->rate_set_id = $request->input('rate_set_id');
        $kurosawa->ng_url = $request->input('ng_url');
        $kurosawa->best_offer = $request->input('best_offer');
        $kurosawa->sku = $request->input('sku');
        $kurosawa->type = $request->input('type');
        $kurosawa->payment_profile = $request->input('payment_profile');
        $kurosawa->return_profile = $request->input('return_profile');
        $kurosawa->shipping_profile = $request->input('shipping_profile');
        $kurosawa->condition = $request->input('condition');
        $kurosawa->template = $request->input('template');
        $kurosawa->priority = $request->input('priority');
        $kurosawa->status = 1;
        $kurosawa->save();
        return redirect('kurosawa');
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
        $kurosawa = Kurosawa::find($id);
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

        return view('kurosawa/edit', compact('kurosawa', 'status_array', 'selector', 'rate_selector', 'template_selector'));
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
        $kurosawa = Kurosawa::find($id);
        $kurosawa->title = $request->input('title');
        $kurosawa->url = $request->input('url');
        $kurosawa->ebay_category = $request->input('ebay_category');
        $kurosawa->ng_keyword = $request->input('ng_keyword');
        $kurosawa->brand_set_id = $request->input('brand_set_id');
        $kurosawa->rate_set_id = $request->input('rate_set_id');
        $kurosawa->ng_url = $request->input('ng_url');
        $kurosawa->best_offer = $request->input('best_offer');
        $kurosawa->sku = $request->input('sku');
        $kurosawa->type = $request->input('type');
        $kurosawa->payment_profile = $request->input('payment_profile');
        $kurosawa->return_profile = $request->input('return_profile');
        $kurosawa->shipping_profile = $request->input('shipping_profile');
        $kurosawa->condition = $request->input('condition');
        $kurosawa->template = $request->input('template');
        $kurosawa->priority = $request->input('priority');
        $kurosawa->status = $request->input('status');
        $kurosawa->save();
        return redirect('kurosawa');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $kurosawa = Kurosawa::find($id);

        return view('kurosawa/delete', compact('kurosawa'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if (KurosawaItem::where('kurosawa_id', $id)->count() > 0) {
            KurosawaItem::where('kurosawa_id', $id)->delete();
        }
        Kurosawa::find($id)->delete();
        return redirect('kurosawa');
    }
}
