<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Digimarts;
use App\Models\DigimartItems;
use App\Models\BrandSet;
use App\Models\RateSet;
use App\Models\templates;
use Illuminate\Support\Facades\App;

class DigimartsController extends Controller
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
        $digimarts = Digimarts::leftJoin('brand_sets', 'digimarts.brand_set_id', '=', 'brand_sets.id')
            ->leftJoin('rate_sets', 'digimarts.rate_set_id', '=', 'rate_sets.id')
            ->select('digimarts.id as id', 'title', 'url', 'ng_keyword', 'brand_set_id', 'rate_set_id', 'ng_url',  'status', 'digimarts.updated_at', 'brand_sets.name as brand_set_name', 'rate_sets.name as rate_set_name', 'priority')
            ->orderBy('priority')
            ->get();
        $items = [];
        foreach ($digimarts as $digimart) {
            $count = DigimartItems::where('digimart_id', $digimart->id)->count();
            $count_jp_content_null = DigimartItems::where('digimart_id', $digimart->id)
                ->whereNull('jp_content')
                ->count();
            $count_en_title_null = DigimartItems::where('digimart_id', $digimart->id)
                ->whereNull('en_title')
                ->count();
            $count_en_brand_null = DigimartItems::where('digimart_id', $digimart->id)
                ->whereNull('en_brand')
                ->count();
            $count_en_content_null = DigimartItems::where('digimart_id', $digimart->id)
                ->whereNull('en_content')
                ->count();
            $count_doller_null = DigimartItems::where('digimart_id', $digimart->id)
                ->whereNull('doller')
                ->count();
            $items[$digimart->id] = [
                'count' => $count,
                'jp_content' => $count_jp_content_null,
                'en_content' => $count_en_content_null,
                'en_title' => $count_en_title_null,
                'en_brand' => $count_en_brand_null,
                'doller' => $count_doller_null,
            ];
        }



        $status_array = $this->status_array;

        return view('digimart/index', compact('digimarts', 'items', 'status_array'));
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

        return view('digimart/create', compact('selector', 'rate_selector', 'template_selector'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $digimart = new Digimarts();
        $digimart->title = $request->input('title');
        $digimart->url = $request->input('url');
        $digimart->ebay_category = $request->input('ebay_category');
        $digimart->ng_keyword = $request->input('ng_keyword');
        $digimart->brand_set_id = $request->input('brand_set_id');
        $digimart->rate_set_id = $request->input('rate_set_id');
        $digimart->ng_url = $request->input('ng_url');
        $digimart->best_offer = $request->input('best_offer');
        $digimart->sku = $request->input('sku');
        $digimart->type = $request->input('type');
        $digimart->payment_profile = $request->input('payment_profile');
        $digimart->return_profile = $request->input('return_profile');
        $digimart->shipping_profile = $request->input('shipping_profile');
        $digimart->condition = $request->input('condition');
        $digimart->template = $request->input('template');
        $digimart->priority = $request->input('priority');
        $digimart->status = 1;
        $digimart->save();
        return redirect('digimart');
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
        $digimart = Digimarts::find($id);
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

        return view('digimart/edit', compact('digimart', 'status_array', 'selector', 'rate_selector', 'template_selector'));
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
        $digimart = Digimarts::find($id);
        $digimart->title = $request->input('title');
        $digimart->url = $request->input('url');
        $digimart->ebay_category = $request->input('ebay_category');
        $digimart->ng_keyword = $request->input('ng_keyword');
        $digimart->brand_set_id = $request->input('brand_set_id');
        $digimart->rate_set_id = $request->input('rate_set_id');
        $digimart->ng_url = $request->input('ng_url');
        $digimart->best_offer = $request->input('best_offer');
        $digimart->sku = $request->input('sku');
        $digimart->type = $request->input('type');
        $digimart->payment_profile = $request->input('payment_profile');
        $digimart->return_profile = $request->input('return_profile');
        $digimart->shipping_profile = $request->input('shipping_profile');
        $digimart->condition = $request->input('condition');
        $digimart->template = $request->input('template');
        $digimart->priority = $request->input('priority');
        $digimart->status = $request->input('status');
        $digimart->save();
        return redirect('digimart');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $digimart = Digimarts::find($id);

        return view('digimart/delete', compact('digimart'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if (DigimartItems::where('digimart_id', $id)->count() > 0) {
            DigimartItems::where('digimart_id', $id)->delete();
        }
        Digimarts::find($id)->delete();
        return redirect('digimart');
    }
}
