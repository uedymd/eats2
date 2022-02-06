<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Secoundstreets;
use App\Models\SecoundstreetItems;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SecoundstreetItemsController extends Controller
{

    private $secoundstreetSearchApi = 'http://15.152.55.85:3000/search/';

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @param int $page
     * @return \Illuminate\Http\Response
     */
    public function items(Request $request, Secoundstreets $secoundstreet, SecoundstreetItems $secoundstreet_items, $id)
    {
        $secoundstreet_data = $secoundstreet->find($id);

        $items = $secoundstreet_items
            ->where('secoundstreet_id', $id)
            ->paginate(150);

        return view('secoundstreet/items', compact('items', 'secoundstreet_data'));
    }

    public function search($id = null)
    {
        if (is_null($id)) {
            $secoundstreets = Secoundstreets::whereIn('status', [1, 3])
                ->leftJoin('brand_sets', 'secoundstreets.brand_set_id', '=', 'brand_sets.id')
                ->select('secoundstreets.id as secoundstreet_id', 'url', 'ng_keyword', 'ng_url', 'brand_sets.set as brand_setting',)
                ->orderBy('checked_at', 'asc')
                ->orderBy('priority')
                ->first();
        } else {
            $secoundstreets = Secoundstreets::whereIn('status', [1, 3])
                ->where('secoundstreets.id', $id)
                ->leftJoin('brand_sets', 'secoundstreets.brand_set_id', '=', 'brand_sets.id')
                ->select('secoundstreets.id as secoundstreet_id', 'url', 'ng_keyword', 'ng_url', 'brand_sets.set as brand_setting',)
                ->orderBy('checked_at', 'asc')
                ->orderBy('priority')
                ->first();
        }
        $setting = Setting::where('site', 'secoundstreet')->first();

        if ($secoundstreets) {

            if ($secoundstreets->brand_setting) {
                $target_brands = str_replace(["\r\n", "\r", "\n"], "\n", $secoundstreets->brand_setting);
                $target_brands = explode("\n", $target_brands);
            } else {
                $target_brands = [];
            }

            if ($secoundstreets) {

                $secoundstreet = urlencode($secoundstreets->url);

                $request = "query={$secoundstreet}";


                $respons = [];
                $url = $this->secoundstreetSearchApi . "?" . $request;


                try {
                    $respons = $this->getApiDataCurl($url);
                } catch (\InvalidArgumentException $e) {
                    echo $e->getMessage() . PHP_EOL;
                }
                if (!empty($respons)) {

                    foreach ((array)$respons as $item) {
                        $secoundstreet_item_count = SecoundstreetItems::where('url', $item['href'])
                            ->count();

                        if ($secoundstreet_item_count == 0) {

                            if ($setting) {
                                $ng_title = $setting->ng_title;
                                $jp_title = $this->format_jp_title($item['title'], $ng_title);
                            } else {
                                $jp_title = $item['title'];
                            }

                            $brand_check = $this->check_title_include_brand($jp_title, $target_brands);

                            if (!empty($jp_title) && $brand_check['result'] && $this->check_url_include_ng_url($item['href'], $secoundstreets->ng_url) === false && $this->check_title_include_ng_keywords($item['title'], $secoundstreets->ng_keyword) === false) {
                                $secoundstreet_item = new secoundstreetItems();
                                $secoundstreet_item->secoundstreet_id = $secoundstreets->secoundstreet_id;
                                $secoundstreet_item->jp_title = $jp_title;
                                $secoundstreet_item->origin_title = $item['title'];
                                $secoundstreet_item->jp_brand = $brand_check['brand'];

                                //ブランド名が英語の場合はen_brandにも入れる
                                if (strlen($brand_check['brand']) == mb_strlen($brand_check['brand'], 'utf8')) {
                                    $secoundstreet_item->en_brand = $brand_check['brand'];
                                }

                                $secoundstreet_item->url = $item['href'];

                                $price = preg_replace('/\s/', '', $item['price']);
                                $price = trim(str_replace(['円', '¥', ',', '税込'], '', $price));

                                $secoundstreet_item->price = $price;

                                $secoundstreet_item->save();
                            }
                        }
                        $this->update_chekced_at($secoundstreets->secoundstreet_id);
                    }
                } else {
                    $this->update_chekced_at($secoundstreets->secoundstreet_id);
                }
            }
        }
        return redirect('secoundstreet');
    }

    private function update_chekced_at($id)
    {
        //デジマートのcheck_at更新
        $current_secoundstreet = Secoundstreets::find($id);
        $check_time = Carbon::now();
        $current_secoundstreet->checked_at = $check_time->format('Y-m-d H:i:s');
        $current_secoundstreet->update();
    }

    public function format_jp_title($title, $ng_title)
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
    private function check_title_include_ng_keywords($title, $ng_keywords)
    {
        if ($ng_keywords) {
            $ng_keyword_array = preg_split("/( |　)+/", $ng_keywords);
            if ($ng_keyword_array) {
                foreach ((array)$ng_keyword_array as $ng_keyword) {
                    $ng_keyword = str_replace('/', '\/', $ng_keyword);
                    $pattern = "/{$ng_keyword}/i";
                    if (preg_match($pattern, $title)) {
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
        $result = [
            'brand' => '',
            'result' => false,
        ];
        array_multisort(array_map("mb_strlen", $brands), SORT_DESC, $brands);
        foreach ((array)$brands as $brand) {
            $brand = str_replace('/', '\/', $brand);
            $pattern = "/{$brand}/i";
            if (preg_match($pattern, $title, $return)) {
                $result['result'] = true;
                $result['brand'] = $return[0];
                break;
            }
        }
        return $result;
    }

    public function get_url()
    {
        $secoundstreet_item = SecoundstreetItems::where('jp_content', NULL)
            ->orWhere('images', NULL)
            ->leftJoin('secoundstreets', 'secoundstreet_items.secoundstreet_id', '=', 'secoundstreets.id')
            ->select('secoundstreet_items.id as id', 'secoundstreet_items.url')
            ->where('secoundstreets.status', '!=', 2)
            ->first();
        return $secoundstreet_item;
    }

    public function get_title()
    {
        $secoundstreet_item = SecoundstreetItems::where('en_title', NULL)
            ->whereNotNull('jp_title')
            ->leftJoin('secoundstreets', 'secoundstreet_items.secoundstreet_id', '=', 'secoundstreets.id')
            ->select('secoundstreet_items.id as id', 'jp_title')
            ->where('secoundstreets.status', '!=', 2)
            ->orderBy('secoundstreet_items.updated_at')->first();
        return $secoundstreet_item;
    }

    public function get_brand()
    {
        $secoundstreet_item = SecoundstreetItems::where('en_brand', NULL)
            ->whereNotNull('jp_brand')
            ->leftJoin('secoundstreets', 'secoundstreet_items.secoundstreet_id', '=', 'secoundstreets.id')
            ->select('secoundstreet_items.id as id', 'jp_brand')
            ->where('secoundstreets.status', '!=', 2)
            ->orderBy('secoundstreets.updated_at')->first();
        return $secoundstreet_item;
    }

    public function get_content()
    {
        $secoundstreet_item = SecoundstreetItems::where('en_content', NULL)
            ->whereNotNull('jp_content')
            ->leftJoin('secoundstreets', 'secoundstreet_items.secoundstreet_id', '=', 'secoundstreets.id')
            ->select('secoundstreet_items.id as id', 'jp_content')
            ->where('secoundstreets.status', '!=', 2)
            ->orderBy('secoundstreet_items.updated_at')->first();
        return $secoundstreet_item;
    }

    public function get_price()
    {
        $secoundstreet_item = SecoundstreetItems::where('doller', NULL)
            ->whereNotNull('price')
            ->join('secoundstreets', 'secoundstreet_items.secoundstreet_id', '=', 'secoundstreets.id')
            ->join('rate_sets', 'secoundstreets.rate_set_id', '=', 'rate_sets.id')
            ->select('secoundstreet_items.id as id', 'secoundstreet_items.price', 'secoundstreet_items.secoundstreet_id', 'rate_sets.set as set')
            ->where('secoundstreets.status', '!=', 2)
            ->where('secoundstreet_items.price', '>', 0)
            ->orderBy('secoundstreet_items.updated_at')->first();

        if ($secoundstreet_item) {

            $rates = unserialize($secoundstreet_item->set);

            $return_price = 0;

            foreach ($rates as $rate) {
                switch ($secoundstreet_item->price) {
                    case empty($rate['min']) && !empty($rate['max']) && $rate['max'] > (float)$secoundstreet_item->price:
                        $return_price = (float)$secoundstreet_item->price + (float)$rate['rate'];
                        break;


                    case !empty($rate['min']) && !empty($rate['max']) && $rate['min'] <= (float)$secoundstreet_item->price && $rate['max'] > (float)$secoundstreet_item->price:
                        $return_price = (float)$secoundstreet_item->price + (float)$rate['rate'];
                        break;

                    case !empty($rate['min']) && empty($rate['max']) && $rate['min'] <= (float)$secoundstreet_item->price:
                        $return_price = (float)$secoundstreet_item->price + (float)$rate['rate'];
                        break;

                    default:
                        break;
                }
                if ($return_price > 0) {
                    break;
                }
            }
            $returns = [
                'id' => $secoundstreet_item->id,
                'price' => $return_price,
            ];
            return $returns;
        }
    }

    public function get_image()
    {
        $secoundstreet_item = SecoundstreetItems::where('images', NULL)
            ->select('id', 'url')
            ->orderBy('updated_at')->first();
        return $secoundstreet_item;
    }

    public function set_content(Request $request)
    {
        if (!empty($request->input('id'))) {
            $secoundstreet_item = SecoundstreetItems::where('id', $request->input('id'))->first();
            $setting = Setting::where('site', 'secoundstreet')->first();
            if (!empty($request->input('content'))) {


                if ($setting) {
                    //除外キーワードを除去（共通設定）
                    $ng_content = $setting->ng_content;
                    $ng_content = str_replace(["\r\n", "\r", "\n"], "\n", $ng_content);
                    $ng_contents = explode("\n", $ng_content);
                } else {
                    $ng_contents = "";
                }

                $jp_content = $this->adjust_jp_content_html($request->input('content'), $ng_contents);


                $secoundstreet_item->jp_content = $jp_content;
                $secoundstreet_item->origin_content = $request->input('content');
                $secoundstreet_item->updated_at = date('Y-m-d H:i:s');
                if ($secoundstreet_item->save() && !empty($jp_content)) {
                    Log::info('nodeからの日本語コンテンツ登録 ID = ' . $request->input('id'));
                } else {
                    Log::info('nodeからの日本語コンテンツ登録スキップ ID = ' . $request->input('id'));
                }
            } else {
                $created =  new \DateTime($secoundstreet_item->created_at);
                $updated =  new \DateTime($secoundstreet_item->updated_at);
                $diff = $updated->diff($created);
                $diff_days = (int)$diff->format('%a');
                if ($diff_days > 0) {
                    $secoundstreet_item->delete();
                    Log::info('nodeからの日本語コンテンツ削除 ID = ' . $request->input('id'));
                } else {
                    $secoundstreet_item->updated_at = date('Y-m-d H:i:s');
                    $secoundstreet_item->save();
                    Log::info('nodeからの日本語コンテンツなし ID = ' . $request->input('id'));
                }
            }

            if (!empty($request->input('images'))) {
                // Log::info($request->input('images'));
                $images = serialize($request->input('images'));

                $secoundstreet_item->images = $images;
                $secoundstreet_item->updated_at = date('Y-m-d H:i:s');
                if ($secoundstreet_item->save()) {
                    Log::info('nodeからの画像登録 ID = ' . $request->input('id'));
                } else {
                    $secoundstreet_item->updated_at = date('Y-m-d H:i:s');
                    $secoundstreet_item->save();
                    Log::info('nodeからの画像登録失敗 ID = ' . $request->input('id'));
                }
            }
        } else {
            Log::error('nodeからの日本語コンテンツ書き込み : IDなし');
        }
    }



    public function delete_content(Request $request)
    {
        if (!empty($request->input('id'))) {
            SecoundstreetItems::where('id', $request->input('id'))->delete();
            Log::info('nodeからの日本語コンテンツ削除 : ' . $request->input('id'));
        } else {
            Log::error('nodeからの日本語コンテンツ書き込み : IDなし');
        }
    }




    private function adjust_jp_content_html($text, $ng_contents)
    {
        //半角カナを全角カナに変換、全角英数字を半角に変換
        $jp_content = mb_convert_kana($text);

        //コンテンツテキストのフォーマット
        $jp_content = $this->format_jp_content($jp_content);

        //コンテンツHTMLのフォーマット
        $jp_content = $this->format_jp_content_html($jp_content);

        //全角文字の除去
        $jp_content = $this->delete_zenkaku_symbol($jp_content);

        $jp_content = trim(preg_replace("/\s{3,}/", "\n", $jp_content));
        //除外キーワードを除去
        $jp_content = str_replace($ng_contents, "", $jp_content);

        $jp_content = trim(preg_replace("/\s{3,}/", "\n", $jp_content));

        return $jp_content;
    }
    private function format_jp_content_html($text)
    {

        //HTMLタグを除去
        $jp_content = strip_tags($text, ["br", "table", "tr", "td", "th", "p"]);
        //改行コードを削除
        $jp_content = preg_replace("/\s/", "", $jp_content);
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

        $jp_content = trim(preg_replace("/\s{3,}/", "\n", $jp_content));

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

        //¥ 123,456,789の除去
        $text = preg_replace("/(¥\s?)?(\d{0,3}\,\d{0,3})+/", "", $text);
        //（税込）の除去
        $text = preg_replace("/(\(税込\)|（税込）)/", "", $text);


        return $text;
    }


    public function set_title(Request $request)
    {
        if (!empty($request->input('id'))) {
            $secoundstreet_item = SecoundstreetItems::where('id', $request->input('id'))->first();
            if (!empty($request->input('content'))) {
                $secoundstreet_item->en_title = $request->input('content');
                $secoundstreet_item->updated_at = date('Y-m-d H:i:s');
                $secoundstreet_item->save();
                Log::info('nodeからの翻訳タイトル登録 ID = ' . $request->input('id'));
            } else {
                $secoundstreet_item->updated_at = date('Y-m-d H:i:s');
                $secoundstreet_item->save();
                Log::info('nodeからの翻訳タイトル失敗 ID = ' . $request->input('id'));
            }
        } else {
            Log::error('nodeからの翻訳タイトル書き込み : IDなし');
        }
    }

    public function set_brand(Request $request)
    {
        if (!empty($request->input('id'))) {
            $secoundstreet_item = SecoundstreetItems::where('id', $request->input('id'))->first();
            if (!empty($request->input('content'))) {
                $secoundstreet_item->en_brand = $request->input('content');
                $secoundstreet_item->updated_at = date('Y-m-d H:i:s');
                $secoundstreet_item->save();
                Log::info('nodeからの翻訳ブランド登録 ID = ' . $request->input('id'));
            } else {
                $secoundstreet_item->updated_at = date('Y-m-d H:i:s');
                $secoundstreet_item->save();
                Log::info('nodeからの翻訳ブランド失敗 ID = ' . $request->input('id'));
            }
        } else {
            Log::error('nodeからの翻訳ブランド書き込み : IDなし');
        }
    }

    public function set_en_content(Request $request)
    {
        if (!empty($request->input('id'))) {
            $secoundstreet_item = SecoundstreetItems::where('id', $request->input('id'))->first();
            if (!empty($request->input('content'))) {
                $secoundstreet_item->en_content = $request->input('content');
                $secoundstreet_item->updated_at = date('Y-m-d H:i:s');
                $secoundstreet_item->save();
                Log::info('nodeからの翻訳コンテンツ登録 ID = ' . $request->input('id'));
            } else {
                $secoundstreet_item->updated_at = date('Y-m-d H:i:s');
                $secoundstreet_item->save();
                Log::info('nodeからの翻訳コンテンツ失敗 ID = ' . $request->input('id'));
            }
        } else {
            Log::error('nodeからの翻訳タイトル書き込み : IDなし');
        }
    }

    public function set_doller(Request $request)
    {
        if (!empty($request->input('id'))) {
            $secoundstreet_item = SecoundstreetItems::where('id', $request->input('id'))->first();
            if (!empty($request->input('doller'))) {
                $secoundstreet_item->doller = ceil($request->input('doller'));
                $secoundstreet_item->updated_at = date('Y-m-d H:i:s');
                $secoundstreet_item->save();
                Log::info('nodeからのドル変換 ID = ' . $request->input('id'));
            } else {
                $secoundstreet_item->updated_at = date('Y-m-d H:i:s');
                $secoundstreet_item->save();
                Log::info('nodeからのドル変換失敗 ID = ' . $request->input('id'));
            }
        } else {
            Log::error('nodeからのドル変換 : IDなし');
        }
    }


    public function recheck(Request $request)
    {
        $secoundstreet_items = SecoundstreetItems::whereNotNull('origin_title')
            ->whereNotNull('origin_content')->get();

        foreach ($secoundstreet_items as $item) {
            $setting = Setting::where('site', 'secoundstreet')->first();
            $origin_title = $item->origin_title;
            $origin_content = $item->origin_content;
            $ng_title = $setting->ng_title;
            $ng_content = $setting->ng_content;
            $ng_content = str_replace(["\r\n", "\r", "\n"], "\n", $ng_content);
            $ng_contents = explode("\n", $ng_content);

            $title = $this->format_jp_title($origin_title, $ng_title);
            $content = $this->adjust_jp_content_html($origin_content, $ng_contents);

            $secoundstreet_item = SecoundstreetItems::find($item->id);
            $secoundstreet_item->jp_title = $title;
            $secoundstreet_item->en_title = null;
            $secoundstreet_item->jp_content = $content;
            $secoundstreet_item->en_content = null;
            $secoundstreet_item->update();
            Log::info('リチェック ID = ' . $item->id);
        }
        return redirect('secoundstreet');
    }



    private function getApiDataCurl($url)
    {
        $option = [
            CURLOPT_RETURNTRANSFER => true, //文字列として返す
            CURLOPT_TIMEOUT        => 3000, // タイムアウト時間
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
