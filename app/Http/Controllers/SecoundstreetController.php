<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Secoundstreets;
use App\Models\SecoundstreetItems;
use App\Models\BrandSet;
use App\Models\RateSet;
use App\Models\templates;
use Illuminate\Support\Facades\App;

class SecoundstreetController extends Controller
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
        $secoundstreets = Secoundstreets::leftJoin('brand_sets', 'secoundstreets.brand_set_id', '=', 'brand_sets.id')
            ->leftJoin('rate_sets', 'secoundstreets.rate_set_id', '=', 'rate_sets.id')
            ->select('secoundstreets.id as id', 'title', 'url', 'ng_keyword', 'brand_set_id', 'rate_set_id', 'ng_url',  'status', 'secoundstreets.checked_at', 'brand_sets.name as brand_set_name', 'rate_sets.name as rate_set_name', 'priority')
            ->orderBy('priority')
            ->get();
        $items = [];
        foreach ($secoundstreets as $secoundstreet) {
            $count = SecoundstreetItems::where('secoundstreet_id', $secoundstreet->id)->count();
            $count_jp_content_null = SecoundstreetItems::where('secoundstreet_id', $secoundstreet->id)
                ->whereNull('jp_content')
                ->count();
            $count_en_title_null = SecoundstreetItems::where('secoundstreet_id', $secoundstreet->id)
                ->whereNull('en_title')
                ->count();
            $count_en_brand_null = SecoundstreetItems::where('secoundstreet_id', $secoundstreet->id)
                ->whereNull('en_brand')
                ->count();
            $count_en_content_null = SecoundstreetItems::where('secoundstreet_id', $secoundstreet->id)
                ->whereNull('en_content')
                ->count();
            $count_doller_null = SecoundstreetItems::where('secoundstreet_id', $secoundstreet->id)
                ->whereNull('doller')
                ->count();
            $items[$secoundstreet->id] = [
                'count' => $count,
                'jp_content' => $count_jp_content_null,
                'en_content' => $count_en_content_null,
                'en_title' => $count_en_title_null,
                'en_brand' => $count_en_brand_null,
                'doller' => $count_doller_null,
            ];
        }



        $status_array = $this->status_array;

        return view('secoundstreet/index', compact('secoundstreets', 'items', 'status_array'));
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

        return view('secoundstreet/create', compact('selector', 'rate_selector', 'template_selector'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $secoundstreet = new Secoundstreets();
        $secoundstreet->title = $request->input('title');
        $secoundstreet->url = $request->input('url');
        $secoundstreet->ebay_category = $request->input('ebay_category');
        $secoundstreet->ng_keyword = $request->input('ng_keyword');
        $secoundstreet->brand_set_id = $request->input('brand_set_id');
        $secoundstreet->rate_set_id = $request->input('rate_set_id');
        $secoundstreet->ng_url = $request->input('ng_url');
        $secoundstreet->best_offer = $request->input('best_offer');
        $secoundstreet->sku = $request->input('sku');
        $secoundstreet->type = $request->input('type');
        $secoundstreet->payment_profile = $request->input('payment_profile');
        $secoundstreet->return_profile = $request->input('return_profile');
        $secoundstreet->shipping_profile = $request->input('shipping_profile');
        $secoundstreet->condition = $request->input('condition');
        $secoundstreet->template = $request->input('template');
        $secoundstreet->priority = $request->input('priority');
        $secoundstreet->status = 1;
        $secoundstreet->save();
        return redirect('secoundstreet');
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
        $secoundstreet = Secoundstreets::find($id);
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

        return view('secoundstreet/edit', compact('secoundstreet', 'status_array', 'selector', 'rate_selector', 'template_selector'));
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
        $secoundstreet = Secoundstreets::find($id);
        $secoundstreet->title = $request->input('title');
        $secoundstreet->url = $request->input('url');
        $secoundstreet->ebay_category = $request->input('ebay_category');
        $secoundstreet->ng_keyword = $request->input('ng_keyword');
        $secoundstreet->brand_set_id = $request->input('brand_set_id');
        $secoundstreet->rate_set_id = $request->input('rate_set_id');
        $secoundstreet->ng_url = $request->input('ng_url');
        $secoundstreet->best_offer = $request->input('best_offer');
        $secoundstreet->sku = $request->input('sku');
        $secoundstreet->type = $request->input('type');
        $secoundstreet->payment_profile = $request->input('payment_profile');
        $secoundstreet->return_profile = $request->input('return_profile');
        $secoundstreet->shipping_profile = $request->input('shipping_profile');
        $secoundstreet->condition = $request->input('condition');
        $secoundstreet->template = $request->input('template');
        $secoundstreet->priority = $request->input('priority');
        $secoundstreet->status = $request->input('status');
        $secoundstreet->save();
        return redirect('secoundstreet');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $secoundstreet = Secoundstreets::find($id);

        return view('secoundstreet/delete', compact('secoundstreet'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if (SecoundstreetItems::where('secoundstreet_id', $id)->count() > 0) {
            SecoundstreetItems::where('secoundstreet_id', $id)->delete();
        }
        Secoundstreets::find($id)->delete();
        return redirect('secoundstreet');
    }
}
