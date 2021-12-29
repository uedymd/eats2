<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hardoff;
use App\Models\HardoffItems;
use App\Models\BrandSet;
use App\Models\RateSet;
use App\Models\templates;
use Illuminate\Support\Facades\App;

class HardoffController extends Controller
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
        $hardoffs = Hardoff::leftJoin('brand_sets', 'hardoffs.brand_set_id', '=', 'brand_sets.id')
            ->leftJoin('rate_sets', 'hardoffs.rate_set_id', '=', 'rate_sets.id')
            ->select('hardoffs.id as id', 'title', 'url', 'ng_keyword', 'brand_set_id', 'rate_set_id', 'ng_url',  'status', 'hardoffs.checked_at', 'brand_sets.name as brand_set_name', 'rate_sets.name as rate_set_name', 'priority')
            ->orderBy('priority')
            ->get();
        $items = [];
        foreach ($hardoffs as $hardoff) {
            $count = HardoffItems::where('hardoff_id', $hardoff->id)->count();
            $count_jp_content_null = HardoffItems::where('hardoff_id', $hardoff->id)
                ->whereNull('jp_content')
                ->count();
            $count_en_title_null = HardoffItems::where('hardoff_id', $hardoff->id)
                ->whereNull('en_title')
                ->count();
            $count_en_brand_null = HardoffItems::where('hardoff_id', $hardoff->id)
                ->whereNull('en_brand')
                ->count();
            $count_en_content_null = HardoffItems::where('hardoff_id', $hardoff->id)
                ->whereNull('en_content')
                ->count();
            $count_doller_null = HardoffItems::where('hardoff_id', $hardoff->id)
                ->whereNull('doller')
                ->count();
            $items[$hardoff->id] = [
                'count' => $count,
                'jp_content' => $count_jp_content_null,
                'en_content' => $count_en_content_null,
                'en_title' => $count_en_title_null,
                'en_brand' => $count_en_brand_null,
                'doller' => $count_doller_null,
            ];
        }



        $status_array = $this->status_array;

        return view('hardoff/index', compact('hardoffs', 'items', 'status_array'));
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

        return view('hardoff/create', compact('selector', 'rate_selector', 'template_selector'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $hardoff = new Hardoff();
        $hardoff->title = $request->input('title');
        $hardoff->url = $request->input('url');
        $hardoff->ebay_category = $request->input('ebay_category');
        $hardoff->ng_keyword = $request->input('ng_keyword');
        $hardoff->brand_set_id = $request->input('brand_set_id');
        $hardoff->rate_set_id = $request->input('rate_set_id');
        $hardoff->ng_url = $request->input('ng_url');
        $hardoff->best_offer = $request->input('best_offer');
        $hardoff->sku = $request->input('sku');
        $hardoff->type = $request->input('type');
        $hardoff->payment_profile = $request->input('payment_profile');
        $hardoff->return_profile = $request->input('return_profile');
        $hardoff->shipping_profile = $request->input('shipping_profile');
        $hardoff->condition = $request->input('condition');
        $hardoff->template = $request->input('template');
        $hardoff->priority = $request->input('priority');
        $hardoff->status = 1;
        $hardoff->save();
        return redirect('hardoff');
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
        $hardoff = Hardoff::find($id);
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

        return view('hardoff/edit', compact('hardoff', 'status_array', 'selector', 'rate_selector', 'template_selector'));
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
        $hardoff = Hardoff::find($id);
        $hardoff->title = $request->input('title');
        $hardoff->url = $request->input('url');
        $hardoff->ebay_category = $request->input('ebay_category');
        $hardoff->ng_keyword = $request->input('ng_keyword');
        $hardoff->brand_set_id = $request->input('brand_set_id');
        $hardoff->rate_set_id = $request->input('rate_set_id');
        $hardoff->ng_url = $request->input('ng_url');
        $hardoff->best_offer = $request->input('best_offer');
        $hardoff->sku = $request->input('sku');
        $hardoff->type = $request->input('type');
        $hardoff->payment_profile = $request->input('payment_profile');
        $hardoff->return_profile = $request->input('return_profile');
        $hardoff->shipping_profile = $request->input('shipping_profile');
        $hardoff->condition = $request->input('condition');
        $hardoff->template = $request->input('template');
        $hardoff->priority = $request->input('priority');
        $hardoff->status = $request->input('status');
        $hardoff->save();
        return redirect('hardoff');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $hardoff = Hardoff::find($id);

        return view('hardoff/delete', compact('hardoff'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if (HardoffItems::where('hardoff_id', $id)->count() > 0) {
            HardoffItems::where('hardoff_id', $id)->delete();
        }
        Hardoff::find($id)->delete();
        return redirect('hardoff');
    }
}
