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
            ->paginate(150);

        return view('rakuten/items', compact('items', 'rakutebn_data'));
    }

    public function search()
    {
        $rakutens = Rakuten::where('status', 1)
            ->leftJoin('brand_sets', 'rakutens.brand_set_id', '=', 'brand_sets.id')
            ->select('rakutens.id as rakuten_id', 'keyword', 'genre_id', 'ng_keyword', 'ng_url', 'price_min', 'price_max', 'brand_sets.set as brand_setting',)
            ->orderBy('checked_at')
            ->first();
        $setting = Setting::where('site', 'rakuten')->first();

        if ($rakutens) {

            if ($rakutens->brand_setting) {
                $target_brands = str_replace(["\r\n", "\r", "\n"], "\n", $rakutens->brand_setting);
                $target_brands = explode("\n", $target_brands);
            } else {
                $target_brands = [];
            }

            if ($rakutens) {

                $rakuten = json_decode($rakutens);

                $request = "applicationId=" . config('app.rakuten_app_id');


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
                    sleep(1);
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

                                //改行を除去 
                                $ng_title = $setting->ng_title;
                                $jp_title = $this->format_jp_title($item['Item']['itemName'], $ng_title);

                                if (!empty($jp_title) && $this->check_title_include_brand($jp_title, $target_brands) && $this->check_url_include_ng_url($item['Item']['itemUrl'], $rakutens->ng_url) === false) {
                                    $rakuten_item = new RakutenItem();
                                    $rakuten_item->rakuten_id = $rakuten->rakuten_id;
                                    $rakuten_item->jp_title = $jp_title;
                                    $rakuten_item->url = $item['Item']['itemUrl'];
                                    $rakuten_item->price = $item['Item']['itemPrice'];
                                    $rakuten_item->save();
                                }
                            }
                        }
                    }
                }
                $rakutens->checked_at = date('Y-m-d H:i:s');
                $rakutens->save();
                return redirect('rakuten');
            }
        }
    }

    private function format_jp_title($title, $ng_title)
    {
        $ng_title = str_replace(["\r\n", "\r", "\n"], "\n", $ng_title);
        $ng_titles = explode("\n", $ng_title);


        //半角カナを全角カナに変換、全角英数字を半角に変換
        $jp_title = mb_convert_kana($title, "KVa");

        // カッコで囲われた部分を除去
        $jp_title = preg_replace('/(【|】)/', ' ', $jp_title);
        $jp_title = preg_replace('/(\[|\])/', ' ', $jp_title);
        $jp_title = preg_replace('/(\(|\))/', ' ', $jp_title);
        $jp_title = preg_replace('/(《|》)/', ' ', $jp_title);
        $jp_title = preg_replace('/(（|）)/', ' ', $jp_title);
        // $jp_title = preg_replace('/【.*?】/', '', $jp_title);
        // $jp_title = preg_replace('/\[.*?\]/', '', $jp_title);
        // $jp_title = preg_replace('/\(.*?\)/', '', $jp_title);
        // $jp_title = preg_replace('/《.*?》/', '', $jp_title);
        // $jp_title = preg_replace('/（.*?）/', '', $jp_title);

        //全角記号を除去
        $jp_title = $this->delete_zenkaku_symbol($jp_title);

        // 除外文言を削除
        $jp_title = str_replace($ng_titles, "", $jp_title);

        //URLエンコード&#160;を削除
        $jp_title = str_replace("&#160;", "", $jp_title);

        // 前後のスペース削除
        $jp_title = trim($jp_title);
        return $jp_title;
    }

    private function delete_zenkaku_symbol($text)
    {
        $symbols = ["，", "．", "・", "：", "；", "゛", "゜", "´", "｀", "¨", "＾", "￣", "＿", "ヽ", "ヾ", "ゝ", "ゞ", "〃", "仝", "〇", "／", "＼", "～", "∥", "｜", "…", "‥", "‘", "’", "“", "”", "±", "×", "÷", "＝", "≠", "＜", "＞", "≦", "≧", "∞", "∴", "♂", "♀", "°", "′", "″", "℃", "￥", "＄", "￠", "￡", "％", "＃", "＆", "＊", "＠", "§", "☆", "★", "○", "●", "◎", "◇", "◆", "□", "■", "△", "▲", "▽", "▼", "※", "〒", "→", "←", "↑", "↓", "〓", "∈", "∋", "⊆", "⊇", "⊂", "⊃", "∪", "∩", "∧", "∨", "￢", "⇒", "⇔", "∀", "∃", "∠", "⊥", "⌒", "∂", "∇", "≡", "≒", "≪", "≫", "√", "∽", "∝", "∵", "∫", "∬", "Å", "‰", "♯", "♭", "♪", "Α", "Β", "Γ", "Δ", "Ε", "Ζ", "Η", "Θ", "Ι", "Κ", "Λ", "Μ", "Ν", "Ξ", "Ο", "Π", "Ρ", "Σ", "Τ", "Υ", "Φ", "Χ", "Ψ", "Ω", "α", "β", "γ", "δ", "ε", "ζ", "η", "θ", "ι", "κ", "λ", "μ", "ν", "ξ", "ο", "π", "ρ", "σ", "τ", "υ", "φ", "χ", "ψ", "ω", "А", "Б", "В", "Г", "Д", "Е", "Ё", "Ж", "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Ч", "Ш", "Щ", "Ъ", "Ы", "Ь", "Э", "Ю", "Я", "а", "б", "в", "г", "д", "е", "ё", "ж", "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ъ", "ы", "ь", "э", "ю", "я", "─", "│", "┌", "┐", "┘", "└", "├", "┬", "┤", "┴", "┼", "━", "┃", "┏", "┓", "┛", "┗", "┣", "┳", "┫", "┻", "╋", "┠", "┯", "┨", "┷", "┿", "┝", "┰", "┥", "┸", "╂", "｡", "｢", "｣", "､", "･", "ｦ", "ｧ", "ｨ", "ｩ", "ｪ", "ｫ", "ｬ", "ｭ", "ｮ", "ｯ", "ｰ", "ｱ", "ｲ", "ｳ", "ｴ", "ｵ", "ｶ", "ｷ", "ｸ", "ｹ", "ｺ", "ｻ", "ｼ", "ｽ", "ｾ", "ｿ", "ﾀ", "ﾁ", "ﾂ", "ﾃ", "ﾄ", "ﾅ", "ﾆ", "ﾇ", "ﾈ", "ﾉ", "ﾊ", "ﾋ", "ﾌ", "ﾍ", "ﾎ", "ﾏ", "ﾐ", "ﾑ", "ﾒ", "ﾓ", "ﾔ", "ﾕ", "ﾖ", "ﾗ", "ﾘ", "ﾙ", "ﾚ", "ﾛ", "ﾜ", "ﾝﾞﾟ", "①", "②", "③", "④", "⑤", "⑥", "⑦", "⑧", "⑨", "⑩", "⑪", "⑫", "⑬", "⑭", "⑮", "⑯", "⑰", "⑱", "⑲", "⑳", "Ⅰ", "Ⅱ", "Ⅲ", "Ⅳ", "Ⅴ", "Ⅵ", "Ⅶ", "Ⅷ", "Ⅸ", "Ⅹ", "㍉", "㌔", "㌢", "㍍", "㌘", "㌧", "㌃", "㌶", "㍑", "㍗", "㌍", "㌦", "㌣", "㌫", "㍊", "㌻", "㎜", "㎝", "㎞", "㎎", "㎏", "㏄", "㎡", "㍻", "〝", "〟", "№", "㏍", "℡", "㊤", "㊥", "㊦", "㊧", "㊨", "㈱", "㈲", "㈹", "㍾", "㍽", "㍼", "≒", "≡", "∫", "∮", "∑", "√", "⊥", "∠", "∟", "⊿", "∵", "∩", "∪", "/", "　", "（", "）", "〔", "〕", "［", "］", "｛", "｝", "〈", "〉", "《", "》", "「", "」", "『", "』", "【", "】", " ﾟ"];

        $return = str_replace($symbols, ' ', $text);
        if ($return) {
            return $return;
        } else {
            return $text;
        }
    }

    private function check_url_include_ng_url($url, $ng_url)
    {
        if ($ng_url) {
            $ng_keywords = preg_split("/( |　)+/", $ng_url);
            if ($ng_keywords) {
                foreach ((array)$ng_keywords as $ng_keyword) {
                    $ng_keyword = str_replace('/', '\/', $ng_keyword);
                    $pattern = "/{$ng_keyword}/i";
                    if (preg_match($pattern, $url)) {
                        return true;
                        break;
                    }
                }
            }
        }
        return false;
    }

    private function check_title_include_brand($title, $brands)
    {
        foreach ((array)$brands as $brand) {
            $brand = str_replace('/', '\/', $brand);
            $pattern = "/{$brand}/i";
            if (preg_match($pattern, $title)) {
                return true;
                break;
            }
        }
        return false;
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
            ->join('rakutens', 'rakuten_items.rakuten_id', '=', 'rakutens.id')
            ->join('rate_sets', 'rakutens.rate_set_id', '=', 'rate_sets.id')
            ->select('rakuten_items.id as id', 'rakuten_items.price', 'rakuten_items.rakuten_id', 'rate_sets.set')
            ->orderBy('rakuten_items.updated_at')->first();

        $rates = unserialize($rakuten_item->set);

        $return_price = 0;

        foreach ($rates as $rate) {
            switch ($rakuten_item->price) {
                case empty($rate['min']) && !empty($rate['max']) && $rate['max'] > $rakuten_item->price:
                    $return_price = $rakuten_item->price + $rate['rate'];
                    break;

                case !empty($rate['min']) && !empty($rate['max']) && $rate['min'] <= $rakuten_item->price && $rate['max'] > $rakuten_item->price:
                    $return_price = $rakuten_item->price + $rate['rate'];
                    break;

                case !empty($rate['min']) && empty($rate['max']) && $rate['min'] <= $rakuten_item->price:
                    $return_price = $rakuten_item->price + $rate['rate'];
                    break;

                default:
                    break;
            }
            if ($return_price > 0) {
                break;
            }
        }

        $returns = [
            'id' => $rakuten_item->id,
            'price' => $return_price,
        ];
        return $returns;
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


                //除外キーワードを除去（共通設定）
                $ng_content = $setting->ng_content;
                $ng_content = str_replace(["\r\n", "\r", "\n"], "\n", $ng_content);
                $ng_contents = explode("\n", $ng_content);

                //半角カナを全角カナに変換、全角英数字を半角に変換
                $jp_content = mb_convert_kana($request->input('content'), "KVa");

                //コンテンツテキストのフォーマット
                $jp_content = $this->format_jp_content($jp_content);

                //コンテンツHTMLのフォーマット
                $jp_content = $this->format_jp_content_html($jp_content);

                //全角文字の除去
                $jp_content = $this->delete_zenkaku_symbol($jp_content);

                //除外キーワードを除去
                $jp_content = str_replace($ng_contents, "", $jp_content);

                $jp_content = trim(preg_replace("/\t/", "", $jp_content));


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

    private function format_jp_content_html($text)
    {

        //HTMLタグを除去
        $jp_content = strip_tags($text, ["br", "table", "tr", "td", "th", "p"]);
        //改行コードを削除
        $jp_content = str_replace(["\r\n", "\r", "\n"], "", $jp_content);
        //<tr>タグの開始タグを除去
        $jp_content = str_replace(["<tr>", "<TR>"], "", $jp_content);
        //thまたはtdに続くth ,tdタグの開始タグをスペースに
        $jp_content = preg_replace("/(<\/th>|<\/td>)+(<td.*?>|<th.*?>)/i", " ", $jp_content);
        //<td>タグの開始タグを除去
        $jp_content = preg_replace("/<td.*?>/i", "", $jp_content);
        //<th>タグの開始タグを除去
        $jp_content = preg_replace("/<th.*?>/i", "", $jp_content);
        //<th><td>タグの綴じタグを除去
        $jp_content = str_replace(["</th>", "</TH>", "</td>", "</TD>",], "", $jp_content);
        // table,pタグの開始タグを<br>に変換
        $jp_content = preg_replace("/(<table.*?>|<p.*?>)/i", "<br>", $jp_content);
        //<tr>タグの綴じタグを<br>に変換
        $jp_content = str_replace(["</table>", "</tr>", "</p>"], "<br>", $jp_content);
        //<br>が3つ以上続くものは除去
        $jp_content = preg_replace("/(<br>|<br \/>){3,}/", "<br>", $jp_content);
        //<br>を改行コードに変換
        $jp_content = str_replace(["<br>", "<br />", "<BR>", "<BR />"], "\n", $jp_content);

        $jp_content = strip_tags($jp_content);

        return $jp_content;
    }
    private function format_jp_content($text)
    {
        //メールアドレスを除去
        $text = preg_replace("/[a-zA-Z0-9_.+-]+[@][a-zA-Z0-9.-]+/", "", $text);
        //電話番号を除去
        $text = preg_replace("/[0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4}/", "", $text);
        //URLを除去
        $text = preg_replace("/(https?|ftp)(:\/\/[-_.!~*'()a-zA-Z0-9;\/?:@&amp;amp;=+$,%#]+)/", "", $text);
        //日付（Y/m/d）を除去
        $text = preg_replace("/[1-9]{1}[0-9]{0,3}\/[0-9]{1,2}\/[0-9]{1,2}/", "", $text);
        //日付（YYYY/mm/dd）を除去
        $text = preg_replace("/(19|20)[0-9]{2}\/\d{2}\/\d{2}/", "", $text);
        //日付（YYYY/mm）を除去
        $text = preg_replace("/(19|20)[0-9]{2}\/(0[1-9]|1[0-2])/", "", $text);

        return $text;
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
                $rakuten_item->doller = ceil($request->input('doller'));
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
