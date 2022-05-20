<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExcludeItemsRequest;
use App\Http\Requests\UpdateExcludeItemsRequest;
use Illuminate\Http\Request;
use App\Models\ExcludeItems;
use App\Models\EbayItem;
use App\Models\Stocks;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExcludeItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd('exclude');
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
     * @param  \App\Http\Requests\StoreExcludeItemsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreExcludeItemsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ExcludeItems  $excludeItems
     * @return \Illuminate\Http\Response
     */
    public function show(ExcludeItems $excludeItems)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ExcludeItems  $excludeItems
     * @return \Illuminate\Http\Response
     */
    public function edit(ExcludeItems $excludeItems)
    {
        $items = ExcludeItems::find(1);
        $items = optional($items);
        return view('exclude_items/edit', compact('items'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateExcludeItemsRequest  $request
     * @param  \App\Models\ExcludeItems  $excludeItems
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $excludes = ExcludeItems::find(1);
        if (is_null($excludes)) {
            $excludes = new ExcludeItems();
        }
        $excludes->keywords = $request->input('keywords');
        $excludes->save();
        return redirect('setting/exclude_items/edit')->with('success', '保存完了');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ExcludeItems  $excludeItems
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExcludeItems $excludeItems)
    {
        //
    }


    public function exclude(ExcludeItems $excludeItems)
    {
        $excludes = ExcludeItems::find(1);
        $keywords = str_replace(array("\r\n", "\r"), "\n", $excludes->keywords);
        $keywords_array = explode("\n", $keywords);
        $items = EbayItem::where('title', 'like', "%{$keywords_array[0]}%");
        for ($i = 1; $i < count($keywords_array); $i++) {
            $items->orWhere('title', 'like', "%" . $keywords_array[$i] . "%");
        }
        $target = $items->get();
        $ebayCtl = app()->make('App\Http\Controllers\EbayItemController');


        foreach ($target as $item) {
            $xml = $ebayCtl->make_delete_item_xml($item);
            $result = $ebayCtl->ebay_delete_item($xml);
            if ($result['Ack'] !== 'Failure' && $result['Ack'] !== 'PartialFailure') {
                Log::info('ebayアイテム削除 ebayリターン成功');
                $target = $ebayCtl->models[$item->site]::find($item->supplier_id)->delete();
                $stock = Stocks::where('site', $item->site)
                    ->where('item_id', $item->supplier_id)->delete();
                if ($target && $stock) {
                    $item->delete();
                }
            } elseif ($result['Errors']['ErrorCode'] == 1047) {
                Log::info('ebayアイテム削除 すでに終了済み');
                $target = $ebayCtl->models[$item->site]::find($item->supplier_id)->delete();
                $stock = Stocks::where('site', $item->site)
                    ->where('item_id', $item->supplier_id)->delete();
                if ($target && $stock) {
                    $item->delete();
                }
            } else {
                $item->status_code = 999;
                $item->error = serialize([0 => '出品取消を失敗しました。']);
                $check_time = Carbon::now();
                $item->tracking_at = $check_time->format('Y-m-d H:i:s');
                Log::info($result);
                Log::info($item->id);
                Log::info($item->ebay_id);
                Log::info('ebayアイテム削除 ebayリターン失敗');
                $item->save();
            }
        }
    }
}
