<?php

namespace App\Http\Controllers\Rakuten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rakuten;
use App\Models\RakutenItem;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class RakutenItemController extends Controller
{

    private $rakutenSearchApi = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20170706';

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @param int $page
     * @return \Illuminate\Http\Response
     */
    public function items(Request $request, Rakuten $rakuten, RakutenItem $rakuten_items, $id)
    {
        $rakutebn_data = $rakuten->find($id);

        $items = $rakuten_items
            ->where('rakuten_id', $id)
            ->paginate(5);

        return view('rakuten/items', compact('items', 'rakutebn_data'));
    }

    public function search()
    {
        $rakutens = Rakuten::where('status', 1)->orderBy('checked_at')->first();
        $setting = Setting::where('site', 'rakuten')->first();

        if ($rakutens) {

            $rakuten = json_decode($rakutens);

            $request = "applicationId={config('app.rakuten_app_id')}";

            if (!empty($rakuten->keyword)) {
                $request .= "&keyword=" . urlencode($rakuten->keyword);
            }
            if (!empty($rakuten->genre_id)) {
                $request .= "&genreId={$rakuten->genre_id}";
            }
            if (!empty($rakuten->ng_keyword)) {
                $request .= "&NGKeyword=" . urlencode($rakuten->ng_keyword);
            }
            if (!empty($rakuten->price_min)) {
                $request .= "&minPrice={$rakuten->price_min}";
            }
            if (!empty($rakuten->price_max)) {
                $request .= "&maxPrice={$rakuten->price_max}";
            }

            $respons = [];
            $url = $this->rakutenSearchApi . "?" . $request;

            for ($i = 1; $i <= 100; $i++) {
                $_request = $request . "&page={$i}";
                $url = $this->rakutenSearchApi . "?" . $_request;
                $_response = $this->getApiDataCurl($url);
                if (!empty($_response)) {
                    $respons[] = $_response;
                } else {
                    break;
                }
            }
            if (!empty($respons)) {

                for ($i = 0; $i < count($respons); $i++) {
                    foreach ((array)$respons[$i]['Items'] as $item) {
                        $rakuten_item_count = RakutenItem::where('url', $item['Item']['itemUrl'])
                            ->count();
                        if (!$rakuten_item_count > 0) {

                            $ng_title = $setting->ng_title;
                            $ng_title = str_replace(["\r\n", "\r", "\n"], "\n", $ng_title);
                            $ng_titles = explode("\n", $ng_title);

                            $jp_title = str_replace($ng_titles, "", $item['Item']['itemName']);

                            $rakuten_item = new RakutenItem();
                            $rakuten_item->rakuten_id = $rakuten->id;
                            $rakuten_item->jp_title = $jp_title;
                            $rakuten_item->url = $item['Item']['itemUrl'];
                            $rakuten_item->price = $item['Item']['itemPrice'];
                            // if (isset($item['Item']['mediumImageUrls'][0]['imageUrl'])) {
                            //     $imgae = serialize($item['Item']['mediumImageUrls'][0]['imageUrl']);
                            //     $rakuten_item->images = $imgae;
                            // }
                            $rakuten_item->save();
                        }
                    }
                }
            }

            $rakutens->checked_at = date('Y-m-d H:i:s');
            $rakutens->save();
            return redirect('rakuten');
        }
    }

    public function get_url()
    {
        $rakuten_item = RakutenItem::where('jp_content', NULL)
            ->orWhere('images', NULL)
            ->select('id', 'url')
            ->orderBy('updated_at', 'asc')
            ->first();
        return $rakuten_item;
    }

    public function get_title()
    {
        $rakuten_item = RakutenItem::where('en_title', NULL)
            ->whereNotNull('jp_title')
            ->select('id', 'jp_title')
            ->orderBy('updated_at')->first();
        return $rakuten_item;
    }

    public function get_content()
    {
        $rakuten_item = RakutenItem::where('en_content', NULL)
            ->whereNotNull('jp_content')
            ->select('id', 'jp_content')
            ->orderBy('updated_at')->first();
        return $rakuten_item;
    }

    public function get_price()
    {
        $rakuten_item = RakutenItem::where('doller', NULL)
            ->whereNotNull('price')
            ->select('id', 'price')
            ->orderBy('updated_at')->first();
        return $rakuten_item;
    }

    public function get_image()
    {
        $rakuten_item = RakutenItem::where('images', NULL)
            ->select('id', 'url')
            ->orderBy('updated_at')->first();
        return $rakuten_item;
    }

    public function set_content(Request $request)
    {
        if (!empty($request->input('id'))) {
            $rakuten_item = RakutenItem::where('id', $request->input('id'))->first();
            $setting = Setting::where('site', 'rakuten')->first();
            if (!empty($request->input('content'))) {

                $ng_content = $setting->ng_content;
                $ng_content = str_replace(["\r\n", "\r", "\n"], "\n", $ng_content);
                $ng_contents = explode("\n", $ng_content);
                $jp_content = str_replace($ng_contents, "", $request->input('content'));


                $rakuten_item->jp_content = $jp_content;
                $rakuten_item->updated_at = date('Y-m-d H:i:s');
                if ($rakuten_item->save() && !empty($jp_content)) {
                    Log::info('nodeからの日本語コンテンツ登録 ID = ' . $request->input('id'));
                } else {
                    Log::info('nodeからの日本語コンテンツ登録スキップ ID = ' . $request->input('id'));
                }
            } else {
                $created =  new \DateTime($rakuten_item->created_at);
                $updated =  new \DateTime($rakuten_item->updated_at);
                $diff = $updated->diff($created);
                $diff_days = (int)$diff->format('%a');
                if ($diff_days > 0) {
                    $rakuten_item->delete();
                    Log::info('nodeからの日本語コンテンツ削除 ID = ' . $request->input('id'));
                } else {
                    $rakuten_item->updated_at = date('Y-m-d H:i:s');
                    $rakuten_item->save();
                    Log::info('nodeからの日本語コンテンツなし ID = ' . $request->input('id'));
                }
            }

            if (!empty($request->input('images'))) {
                // Log::info($request->input('images'));
                $images = serialize($request->input('images'));

                $rakuten_item->images = $images;
                $rakuten_item->updated_at = date('Y-m-d H:i:s');
                if ($rakuten_item->save()) {
                    Log::info('nodeからの画像登録 ID = ' . $request->input('id'));
                } else {
                    $rakuten_item->updated_at = date('Y-m-d H:i:s');
                    $rakuten_item->save();
                    Log::info('nodeからの画像登録失敗 ID = ' . $request->input('id'));
                }
            }
        } else {
            Log::error('nodeからの日本語コンテンツ書き込み : IDなし');
        }
    }


    public function set_title(Request $request)
    {
        if (!empty($request->input('id'))) {
            $rakuten_item = RakutenItem::where('id', $request->input('id'))->first();
            if (!empty($request->input('content'))) {
                $rakuten_item->en_title = $request->input('content');
                $rakuten_item->updated_at = date('Y-m-d H:i:s');
                $rakuten_item->save();
                Log::info('nodeからの翻訳タイトル登録 ID = ' . $request->input('id'));
            } else {
                $rakuten_item->updated_at = date('Y-m-d H:i:s');
                $rakuten_item->save();
                Log::info('nodeからの翻訳タイトル失敗 ID = ' . $request->input('id'));
            }
        } else {
            Log::error('nodeからの翻訳タイトル書き込み : IDなし');
        }
    }

    public function set_en_content(Request $request)
    {
        if (!empty($request->input('id'))) {
            $rakuten_item = RakutenItem::where('id', $request->input('id'))->first();
            if (!empty($request->input('content'))) {
                $rakuten_item->en_content = $request->input('content');
                $rakuten_item->updated_at = date('Y-m-d H:i:s');
                $rakuten_item->save();
                Log::info('nodeからの翻訳コンテンツ登録 ID = ' . $request->input('id'));
            } else {
                $rakuten_item->updated_at = date('Y-m-d H:i:s');
                $rakuten_item->save();
                Log::info('nodeからの翻訳コンテンツ失敗 ID = ' . $request->input('id'));
            }
        } else {
            Log::error('nodeからの翻訳タイトル書き込み : IDなし');
        }
    }

    public function set_doller(Request $request)
    {
        if (!empty($request->input('id'))) {
            $rakuten_item = RakutenItem::where('id', $request->input('id'))->first();
            if (!empty($request->input('doller'))) {
                $rakuten = Rakuten::find($rakuten_item->rakuten_id);
                $rakuten_item->doller = ceil($request->input('doller') * $rakuten->rate);
                $rakuten_item->updated_at = date('Y-m-d H:i:s');
                $rakuten_item->save();
                Log::info('nodeからのドル変換 ID = ' . $request->input('id'));
            } else {
                $rakuten_item->updated_at = date('Y-m-d H:i:s');
                $rakuten_item->save();
                Log::info('nodeからのドル変換失敗 ID = ' . $request->input('id'));
            }
        } else {
            Log::error('nodeからのドル変換 : IDなし');
        }
    }



    private function getApiDataCurl($url)
    {
        $option = [
            CURLOPT_RETURNTRANSFER => true, //文字列として返す
            CURLOPT_TIMEOUT        => 3, // タイムアウト時間
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, $option);

        $json    = curl_exec($ch);
        $info    = curl_getinfo($ch);
        $errorNo = curl_errno($ch);

        // OK以外はエラーなので空白配列を返す
        if ($errorNo !== CURLE_OK) {
            // 詳しくエラーハンドリングしたい場合はerrorNoで確認
            // タイムアウトの場合はCURLE_OPERATION_TIMEDOUT
            return [];
        }

        // 200以外のステータスコードは失敗とみなし空配列を返す
        if ($info['http_code'] !== 200) {
            return [];
        }

        // 文字列から変換
        $jsonArray = json_decode($json, true);

        return $jsonArray;
    }
}
