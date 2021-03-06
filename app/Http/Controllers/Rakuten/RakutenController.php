<?php

namespace App\Http\Controllers\Rakuten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rakuten;
use App\Models\RakutenItem;
use App\Models\BrandSet;
use App\Models\RateSet;
use App\Models\templates;
use Illuminate\Support\Facades\App;

class RakutenController extends Controller
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
        $rakutens = Rakuten::leftJoin('brand_sets', 'rakutens.brand_set_id', '=', 'brand_sets.id')
            ->leftJoin('rate_sets', 'rakutens.rate_set_id', '=', 'rate_sets.id')
            ->select('rakutens.id as id', 'title', 'keyword', 'genre', 'genre_id', 'ng_keyword', 'brand_set_id', 'rate_set_id', 'ng_url', 'price_max', 'price_min', 'status', 'rakutens.updated_at', 'brand_sets.name as brand_set_name', 'rate_sets.name as rate_set_name', 'priority')
            ->orderBy('priority')
            ->get();
        $items = [];
        foreach ($rakutens as $rakuten) {
            $count = RakutenItem::where('rakuten_id', $rakuten->id)->count();
            $count_jp_content_null = RakutenItem::where('rakuten_id', $rakuten->id)
                ->whereNull('jp_content')
                ->count();
            $count_en_title_null = RakutenItem::where('rakuten_id', $rakuten->id)
                ->whereNull('en_title')
                ->count();
            $count_en_brand_null = RakutenItem::where('rakuten_id', $rakuten->id)
                ->whereNull('en_brand')
                ->count();
            $count_en_content_null = RakutenItem::where('rakuten_id', $rakuten->id)
                ->whereNull('en_content')
                ->count();
            $count_doller_null = RakutenItem::where('rakuten_id', $rakuten->id)
                ->whereNull('doller')
                ->count();
            $items[$rakuten->id] = [
                'count' => $count,
                'jp_content' => $count_jp_content_null,
                'en_content' => $count_en_content_null,
                'en_title' => $count_en_title_null,
                'en_brand' => $count_en_brand_null,
                'doller' => $count_doller_null,
            ];
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

        return view('rakuten/create', compact('selector', 'rate_selector', 'template_selector'));
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
        $rakuten->ebay_category = $request->input('ebay_category');
        $rakuten->ng_keyword = $request->input('ng_keyword');
        $rakuten->brand_set_id = $request->input('brand_set_id');
        $rakuten->rate_set_id = $request->input('rate_set_id');
        $rakuten->ng_url = $request->input('ng_url');
        $rakuten->ng_url = $request->input('ng_url');
        $rakuten->price_min = $request->input('price_min');
        $rakuten->price_max = $request->input('price_max');
        $rakuten->best_offer = $request->input('best_offer');
        $rakuten->sku = $request->input('sku');
        $rakuten->type = $request->input('type');
        $rakuten->payment_profile = $request->input('payment_profile');
        $rakuten->return_profile = $request->input('return_profile');
        $rakuten->shipping_profile = $request->input('shipping_profile');
        $rakuten->condition = $request->input('condition');
        $rakuten->template = $request->input('template');
        $rakuten->priority = $request->input('priority');
        $rakuten->status = 1;
        $rakuten->save();
        return redirect('rakuten');
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

        return view('rakuten/edit', compact('rakuten', 'status_array', 'selector', 'rate_selector', 'template_selector'));
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
        $rakuten->ebay_category = $request->input('ebay_category');
        $rakuten->ng_keyword = $request->input('ng_keyword');
        $rakuten->brand_set_id = $request->input('brand_set_id');
        $rakuten->rate_set_id = $request->input('rate_set_id');
        $rakuten->ng_url = $request->input('ng_url');
        $rakuten->ng_url = $request->input('ng_url');
        $rakuten->price_min = $request->input('price_min');
        $rakuten->price_max = $request->input('price_max');
        $rakuten->best_offer = $request->input('best_offer');
        $rakuten->sku = $request->input('sku');
        $rakuten->type = $request->input('type');
        $rakuten->payment_profile = $request->input('payment_profile');
        $rakuten->return_profile = $request->input('return_profile');
        $rakuten->shipping_profile = $request->input('shipping_profile');
        $rakuten->condition = $request->input('condition');
        $rakuten->template = $request->input('template');
        $rakuten->priority = $request->input('priority');
        $rakuten->status = $request->input('status');
        $rakuten->save();
        return redirect('rakuten');
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
        if (RakutenItem::where('rakuten_id', $id)->delete()) {
            $rakuten = Rakuten::find($id);
            $rakuten->delete();
        }
        return redirect('rakuten');
    }
}
