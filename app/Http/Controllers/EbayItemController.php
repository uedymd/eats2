<?php

namespace App\Http\Controllers;

use App\Models\EbayItem;
use Illuminate\Http\Request;
use App\Models\Rakuten;
use App\Models\RakutenItem;
use App\Models\Digimarts;
use App\Models\DigimartItems;
use App\Models\Hardoff;
use App\Models\HardoffItems;
use App\Models\SecoundstreetItems;
use App\Models\Secoundstreets;
use App\Models\templates;
use App\Models\Stocks;
use Carbon\Carbon;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Illuminate\Support\Facades\Log;

class EbayItemController extends Controller
{


    private $api_url = 'https://api.ebay.com/ws/api.dll';
    // private $url = 'https://api.sandbox.ebay.com/ws/api.dll';

    public $models = [
        'rakuten' => 'App\Models\RakutenItem',
        'digimart' => 'App\Models\DigimartItems',
        'hardoff' => 'App\Models\HardoffItems',
        'secoundstreet' => 'App\Models\SecoundstreetItems',
    ];
    private $sites = [
        'rakuten' => 'App\Models\Rakuten',
        'digimart' => 'App\Models\Digimarts',
        'hardoff' => 'App\Models\Hardoff',
        'secoundstreet' => 'App\Models\Secoundstreets',
    ];


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ebay_items = EbayItem::orderBy('id', 'desc')->paginate(150);

        $suppliers = [];

        foreach ($ebay_items as $ebay_item) {
            switch ($ebay_item->site) {
                case 'rakuten':
                    $rakuten_item = RakutenItem::find($ebay_item->supplier_id);
                    if ($rakuten_item) {
                        $suppliers[$ebay_item->id] = $rakuten_item->url;
                    }
                    break;
                case 'digimart':
                    $digimart_item = DigimartItems::find($ebay_item->supplier_id);
                    if ($digimart_item) {
                        $suppliers[$ebay_item->id] = $digimart_item->url;
                    }
                    break;

                case 'hardoff':
                    $hardoff_item = HardoffItems::find($ebay_item->supplier_id);
                    if ($hardoff_item) {
                        $suppliers[$ebay_item->id] = $hardoff_item->url;
                    }
                    break;

                case 'secoundstreet':
                    $secoundstreet_item = SecoundstreetItems::find($ebay_item->supplier_id);
                    if ($secoundstreet_item) {
                        $suppliers[$ebay_item->id] = $secoundstreet_item->url;
                    }
                    break;

                default:
                    # code...
                    break;
            }
        }
        $keyword = "";
        return view('ebay/index', compact('ebay_items', 'suppliers', 'keyword'));
    }



    public function search(Request $request)
    {
        $keyword = $request->keyword;

        $ebay_items = EbayItem::orderBy('id', 'desc')
            ->orWhere('title', 'like', '%' . $keyword . '%')
            ->orWhere('ebay_id', 'like', $keyword)
            ->paginate(150);;

        $suppliers = [];

        foreach ($ebay_items as $ebay_item) {
            switch ($ebay_item->site) {
                case 'rakuten':
                    $rakuten_item = RakutenItem::find($ebay_item->supplier_id);
                    if ($rakuten_item) {
                        $suppliers[$ebay_item->id] = $rakuten_item->url;
                    }
                    break;
                case 'digimart':
                    $digimart_item = DigimartItems::find($ebay_item->supplier_id);
                    if ($digimart_item) {
                        $suppliers[$ebay_item->id] = $digimart_item->url;
                    }
                    break;

                case 'hardoff':
                    $hardoff_item = HardoffItems::find($ebay_item->supplier_id);
                    if ($hardoff_item) {
                        $suppliers[$ebay_item->id] = $hardoff_item->url;
                    }
                    break;

                case 'secoundstreet':
                    $secoundstreet_item = SecoundstreetItems::find($ebay_item->supplier_id);
                    if ($secoundstreet_item) {
                        $suppliers[$ebay_item->id] = $secoundstreet_item->url;
                    }
                    break;

                default:
                    # code...
                    break;
            }
        }
        return view('ebay/index', compact('ebay_items', 'suppliers', 'keyword'));
    }

    /**
     * 
     *
     * @return \Illuminate\Http\Response
     */
    public function tracking($site)
    {
        $items = "";
        $items = EbayItem::select('ebay_items.id', "{$site}_items.url")
            ->join("{$site}_items", function ($join) use ($site) {
                $join->on('ebay_items.supplier_id', '=', "{$site}_items.id");
                // ->where('ebay_items.status_code', '<=', 200);
            })
            // ->leftJoin("{$site}_items", 'ebay_items.supplier_id', '=', "{$site}_items.id")
            ->where('ebay_items.site', $site)
            ->orderBy('ebay_items.tracking_at')
            ->orderBy('ebay_items.created_at')
            ->first();

        return $items;
    }

    /**
     * 
     *
     * @return \Illuminate\Http\Response
     */
    public function set_tracking(Request $request, $site)
    {

        $returns = [];
        $erros = [];
        $ebay_item = EbayItem::find($request['id']);

        Log::info($request['result']['status']);


        if ($request['result']['status'] >= 400 && $request['result']['status'] < 500) {
            Log::info("ebay?????????????????? ??????????????? = {$site}???" . $request['result']['status']);
            $this->destroy(new EbayItem, $ebay_item->id);
            return false;
        }

        if ($request['result']['check']) {

            $site_item = $this->models[$site]::find($ebay_item->supplier_id);
            $result_price = preg_replace("/[^0-9]+/", '', $request['result']['price']);
            $result_price = preg_replace('/\s/', '', $result_price);
            $result_price = trim(str_replace(['???', '??', ',', '??????'], '', $result_price));

            if ((float)$site_item->price < (float)$request['result']['price']) {
                $erros[] = "?????????????????????????????????????????????{$site_item->price}??? => {$request['result']['price']}???";
                switch ($site) {
                    case 'rakuten':
                        $rakuten_items = RakutenItem::find($ebay_item->supplier_id);
                        if (!empty($rakuten_items->price) && $rakuten_items->price > 0) {
                            $rakuten_items->price = (float)$result_price;
                            $rakuten_items->save();
                        }
                        $returns[] = "??????????????????";
                        Log::info('?????????????????? ID = ' . $ebay_item->id);
                        break;
                    case 'digimart':
                        $digimart_items = DigimartItems::find($ebay_item->supplier_id);
                        if (!empty($digimart_items->price) && $digimart_items->price > 0) {
                            $digimart_items->price = $result_price;
                            $digimart_items->save();
                        }
                        $returns[] = "???????????????????????????";
                        Log::info('??????????????????????????? ID = ' . $ebay_item->id);
                        break;
                    case 'hardoff':
                        $hardoff_items = HardoffItems::find($ebay_item->supplier_id);
                        if (!empty($hardoff_items->price) && $hardoff_items->price > 0) {
                            $hardoff_items->price = $result_price;
                            $hardoff_items->save();
                        }
                        $returns[] = "Hardoff????????????";
                        Log::info('Hardoff???????????? ID = ' . $ebay_item->id);
                        break;

                    case 'secoundstreet':
                        $secoundstreet_items = SecoundstreetItems::find($ebay_item->supplier_id);
                        if (!empty($secoundstreet_items->price) && $secoundstreet_items->price > 0) {
                            $secoundstreet_items->price = $result_price;
                            $secoundstreet_items->save();
                        }
                        $returns[] = "SecoundStreet????????????";
                        Log::info('SecoundStreet???????????? ID = ' . $ebay_item->id);
                        break;

                    default:
                        # code...
                        break;
                }
            }
            if ($request['result']['status'] >= 500) {
                $datetime = date('Y???m???d??? H:i');
                $erros[] = "???????????????????????????????????????{$datetime}????????????????????????????????????????????????";
                Log::info('??????????????????????????????????????? ID = ' . $ebay_item->id);
            }
            if (!empty($erros)) {
                $ebay_item->error = serialize($erros);
            } else {
                $ebay_item->error = null;
            }
            $check_time = Carbon::now();
            $ebay_item->tracking_at = $check_time->format('Y-m-d H:i:s');
            $ebay_item->status_code = $request['result']['status'];
            $ebay_item->update();
            $returns[] = "ebay_item?????????{$check_time->format('Y-m-d H:i:s')}";
            Log::info('ebay_item????????? ID = ' . $ebay_item->id);
        } else {
            Log::info("ebay?????????????????? ??????????????? = {$site}???" . $request['result']['status']);
            if ($request['result']['status'] < 500) {

                $this->destroy(new EbayItem, $ebay_item->id);
                $returns[] = "ebay_item?????????ID {$ebay_item->id}";
                Log::info('ebay?????????????????? ID = ' . $ebay_item->id);
            }
        }
        return $returns;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EbayItem  $ebayItem
     * @return \Illuminate\Http\Response
     */
    public function show(EbayItem $ebayItem, $id)
    {
        $ebay = EbayItem::find($id);
        $target = $this->models[$ebay->site]::find($ebay->supplier_id);
        $suppliers = [];

        switch ($ebay->site) {
            case 'rakuten':
                $rakuten_item = RakutenItem::find($ebay->supplier_id);
                if ($rakuten_item) {
                    $suppliers[$ebay->id] = $rakuten_item->url;
                }
                break;
            case 'digimart':
                $digimart_item = DigimartItems::find($ebay->supplier_id);
                if ($digimart_item) {
                    $suppliers[$ebay->id] = $digimart_item->url;
                }
                break;

            case 'hardoff':
                $hardoff_item = HardoffItems::find($ebay->supplier_id);
                if ($hardoff_item) {
                    $suppliers[$ebay->id] = $hardoff_item->url;
                }
                break;

            case 'secoundstreet':
                $secoundstreet_item = SecoundstreetItems::find($ebay->supplier_id);
                if ($secoundstreet_item) {
                    $suppliers[$ebay->id] = $secoundstreet_item->url;
                }
                break;

            default:
                # code...
                break;
        }

        return view('ebay/show', compact('ebay', 'suppliers', 'target'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EbayItem  $ebayItem
     * @return \Illuminate\Http\Response
     */
    public function edit(EbayItem $ebayItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EbayItem  $ebayItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EbayItem $ebayItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EbayItem  $ebayItem
     * @return \Illuminate\Http\Response
     */
    public function delete(EbayItem $ebayItem, $id)
    {
        $ebay = EbayItem::find($id);

        return view('ebay/delete', compact('ebay'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EbayItem  $ebayItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(EbayItem $ebayItem, $id)
    {
        $item = $ebayItem::find($id);
        $xml = $this->make_delete_item_xml($item);

        $result = $this->ebay_delete_item($xml);
        if ($result['Ack'] !== 'Failure' && $result['Ack'] !== 'PartialFailure') {
            Log::info('ebay?????????????????? ebay??????????????????');
            $target = $this->models[$item->site]::find($item->supplier_id)->delete();
            $stock = Stocks::where('site', $item->site)
                ->where('item_id', $item->supplier_id)->delete();
            if ($target && $stock) {
                $item->delete();
            }
        } elseif ($result['Errors']['ErrorCode'] == 1047) {
            Log::info('ebay?????????????????? ?????????????????????');
            $target = $this->models[$item->site]::find($item->supplier_id)->delete();
            $stock = Stocks::where('site', $item->site)
                ->where('item_id', $item->supplier_id)->delete();
            if ($target && $stock) {
                $item->delete();
            }
        } else {
            $item->status_code = 999;
            $item->error = serialize([0 => '????????????????????????????????????']);
            $check_time = Carbon::now();
            $item->tracking_at = $check_time->format('Y-m-d H:i:s');
            Log::info($result);
            Log::info('ebay?????????????????? ebay??????????????????');
            $item->save();
        }

        return redirect('ebay/trading');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EbayItem  $ebayItem
     * @return \Illuminate\Http\Response
     */
    public function add(EbayItem $ebayItem, $site, $id = null)
    {


        if (is_null($id)) {
            $item = $this->models[$site]::leftJoin('stocks', $site . '_items.id', '=', 'stocks.item_id')
                ->where('stocks.site', $site)
                ->where('stocks.status', 1)
                ->first();

            if (is_null($item)) {
                return false;
            }
            $id = $item->item_id;
        }


        $item = $this->models[$site]::find($id);

        try {
            $xml = $this->make_add_item_xml($item, $site);
        } catch (\Exception $e) {

            $update = [
                'error' => "XML????????????{$e->getMessage()}",
                'status' => 3
            ];
            $stock = Stocks::where('site', $site)
                ->where('item_id', $id)->update($update);
        }
        $registed_item = $this->ebay_regist_item($xml);
        if ($registed_item['Ack'] !== 'Failure') {
            $ebay_item = new EbayItem();
            $ebay_item->ebay_id = $registed_item['ItemID'];
            $ebay_item->site = $site;
            $ebay_item->supplier_id = $id;
            $ebay_item->title = $item->en_title;
            $ebay_item->price = $item->doller;
            if ($ebay_item->save()) {
                $stock = Stocks::where('site', $site)
                    ->where('item_id', $id)->first();
                $stock->status = 2;
                $stock->save();
            }
        } else {
            $update = [
                'error' => serialize($registed_item['Errors']),
                'status' => 3
            ];
            $stock = Stocks::where('site', $site)
                ->where('item_id', $id)->update($update);
            // $stock->error = serialize($registed_item['Errors']);
            // $stock->status = 3;
            // $stock->save();
        }
        dd($registed_item);
    }


    private function make_add_item_xml($item, $site)
    {
        switch ($site) {
            case 'rakuten':
                $item_settings = $this->sites[$site]::find($item->rakuten_id);
                break;

            case 'digimart':
                $item_settings = $this->sites[$site]::find($item->digimart_id);
                break;

            case 'hardoff':
                $item_settings = $this->sites[$site]::find($item->hardoff_id);
                break;

            case 'secoundstreet':
                $item_settings = $this->sites[$site]::find($item->secoundstreet_id);
                break;

            default:
                # code...
                break;
        }



        if ($item_settings->condition == 2) {
            $condtionID = 3000;
        } else {
            $condtionID = 1000;
        }
        $category = $item_settings->ebay_category;
        $Duration = 'GTC';
        $MinimumBestOfferPrice = '';
        $location = 'OSAKA';
        $Quantity = 1;
        $PaymentProfileName = $item_settings->payment_profile;
        $ReturnProfileName = $item_settings->return_profile;
        $ShippingProfileName = $item_settings->shipping_profile;
        $type = $item_settings->type;
        $brand = htmlspecialchars($item->en_brand);
        $sku = $item_settings->sku;
        $doller = (int)$item->doller;

        $text = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $text .= "<AddFixedPriceItemRequest xmlns=\"urn:ebay:apis:eBLBaseComponents\">\n";
        $text .= "<RequesterCredentials>\n";
        $text .= "<eBayAuthToken>" . config('app.ebay_token') . "</eBayAuthToken>\n";
        $text .= "</RequesterCredentials>\n";
        $text .= "<ErrorLanguage>en_US</ErrorLanguage>\n";
        $text .= "<Item>\n";
        $text .= "<Title>" . htmlspecialchars(mb_strimwidth($item->en_title, 0, 80)) . "</Title>\n";
        $description = $this->make_description_html($item, $site);
        $text .= "<Description><![CDATA[" . $description . "]]></Description>\n";
        $text .= "<PrimaryCategory><CategoryID>{$category}</CategoryID></PrimaryCategory>\n";
        $text .= "<StartPrice>{$doller}</StartPrice>\n";
        $text .= "<AutoPay>0</AutoPay>\n";
        $text .= "<CategoryMappingAllowed>true</CategoryMappingAllowed>\n";
        $text .= "<ConditionID>{$condtionID}</ConditionID>\n";
        $text .= "<Country>JP</Country>\n";
        $text .= "<Currency>USD</Currency>\n";
        $text .= "<DispatchTimeMax>5</DispatchTimeMax>\n";
        // if (!empty($sku)) {
        //     $text .= "<InventoryTrackingMethod>SKU</InventoryTrackingMethod>\n";
        // }
        $text .= "<ItemSpecifics>\n";
        $text .= "<NameValueList>\n";
        $text .= "<Name>Type</Name>\n";
        $text .= "<Value>{$type}</Value>\n";
        $text .= "</NameValueList>\n";
        $text .= "<NameValueList>\n";
        $text .= "<Name>Brand</Name>\n";
        $text .= "<Value>{$brand}</Value>\n";
        $text .= "</NameValueList>\n";
        // if (array_key_exists("C:Model", $data) && !empty($data['C:Model'])) {
        //     $text .= "<NameValueList>\n";
        //     $text .= "<Name>Model</Name>\n";
        //     $text .= "<Value>" . htmlspecialchars($data['C:Model']) . "</Value>";
        //     $text .= "</NameValueList>\n";
        // }
        // if (array_key_exists("*C:MPN", $data) && !empty($data['*C:MPN'])) {
        //     $text .= "<NameValueList>\n";
        //     $text .= "<Name>MPN</Name>\n";
        //     $text .= "<Value>" . htmlspecialchars($data['*C:MPN']) . "</Value>\n";
        //     $text .= "</NameValueList>\n";
        // }
        // if (array_key_exists("C:Body Type", $data) && !empty($data['C:Body Type'])) {
        //     $text .= "<NameValueList> \n";
        //     $text .= "<Name>Body Type</Name>\n";
        //     $text .= "<Value>" . htmlspecialchars($data['C:Body Type']) . "</Value>\n";
        //     $text .= "</NameValueList>\n";
        // }
        // if (array_key_exists("C:String Configuration", $data) && !empty($data['C:String Configuration'])) {
        //     $text .= "<NameValueList>\n";
        //     $text .= "<Name>String Configuration</Name>\n";
        //     $text .= "<Value>" . htmlspecialchars($data['C:String Configuration']) . "</Value>\n";
        //     $text .= "</NameValueList>\n";
        // }
        // if (array_key_exists("C:Dexterity", $data) && !empty($data['C:Dexterity'])) {
        //     $text .= "<NameValueList>\n";
        //     $text .= "<Name>Dexterity</Name>\n";
        //     $text .= "<Value>" . htmlspecialchars($data['C:Dexterity']) . "</Value>\n";
        //     $text .= "</NameValueList>\n";
        // }
        // if (array_key_exists("C:Body Color", $data) && !empty($data['C:Body Color'])) {
        //     $text .= "<NameValueList>\n";
        //     $text .= "<Name>Body Color</Name>\n";
        //     $text .= "<Value>" . htmlspecialchars($data['C:Body Color']) . "</Value>\n";
        //     $text .= "</NameValueList>\n";
        // }
        // if (array_key_exists("C:Body Material", $data) && !empty($data['C:Body Material'])) {
        //     $text .= "<NameValueList>\n";
        //     $text .= "<Name>Body Material</Name>\n";
        //     $text .= "<Value>" . htmlspecialchars($data['C:Body Material']) . "</Value>\n";
        //     $text .= "</NameValueList>\n";
        // }
        // if (array_key_exists("C:Country/Region of Manufacture", $data) && !empty($data['C:Country/Region of Manufacture'])) {
        //     $text .= "<NameValueList>\n";
        //     $text .= "<Name>Country/Region of Manufacture</Name>\n";
        //     $text .= "<Value>" . htmlspecialchars($data['C:Country/Region of Manufacture']) . "</Value>\n";
        //     $text .= "</NameValueList>\n";
        // }
        $text .= "</ItemSpecifics>\n";
        if (!empty($Duration)) {
            if ($Duration == "GTC") {
                $_duration = "GTC";
            } else {
                $_duration = "Days_{$Duration}";
            }
            $text .= "<ListingDuration>{$_duration}</ListingDuration>\n";
        }
        $text .= "<ListingType>FixedPriceItem</ListingType>\n";
        // if (array_key_exists("MinimumBestOfferPrice", $data) && !empty($data['MinimumBestOfferPrice'])) {
        //     $text .= "<ListingDetails>\n";
        //     $text .= "<MinimumBestOfferPrice>{$data['MinimumBestOfferPrice']}</MinimumBestOfferPrice>\n";
        //     $text .=  "</ListingDetails>\n";
        // }
        $text .= "<Location>{$location}</Location>\n";

        $text .= "<PictureDetails>\n";
        $text .= "<GalleryType>Gallery</GalleryType>\n";

        $imgaes = unserialize($item->images);
        $count = 0;
        foreach ($imgaes as $picture) {
            if (!empty($picture)) {
                $text .= "<PictureURL>{$picture}</PictureURL>\n";
                $count++;
                if ($count > 11) {
                    break;
                }
            }
        }
        $text .= "</PictureDetails>\n";
        // if (array_key_exists("PostalCode", $data) && !empty($data['PostalCode'])) {
        //     $text .= "<PostalCode>{$data['PostalCode']}</PostalCode>\n";
        // }


        $text .= "<ProductListingDetails>";
        // if (array_key_exists("Product:UPC", $data) && !empty($data['Product:UPC'])) {
        //     $text .= "<UPC>{$data['Product:UPC']}</UPC>\n";
        // }
        $text .= "<IncludeStockPhotoURL>true</IncludeStockPhotoURL>\n";
        $text .= "<IncludeeBayProductDetails>true</IncludeeBayProductDetails>\n";
        $text .= "<UseFirstProduct>true</UseFirstProduct>\n";
        $text .= "<UseStockPhotoURLAsGallery>true</UseStockPhotoURLAsGallery>\n";
        $text .= "<ReturnSearchResultOnDuplicates>true</ReturnSearchResultOnDuplicates>\n";
        $text .= "</ProductListingDetails>\n";



        $text .= "<Quantity>{$Quantity}</Quantity>\n";
        $text .= "<SellerProfiles>\n";
        $text .= "<SellerPaymentProfile>\n";
        $text .= "<PaymentProfileName>{$PaymentProfileName}</PaymentProfileName>\n";
        $text .= "</SellerPaymentProfile>\n";
        $text .= "<SellerReturnProfile>\n";
        $text .= "<ReturnProfileName>{$ReturnProfileName}</ReturnProfileName>\n";
        $text .= "</SellerReturnProfile>\n";
        $text .= "<SellerShippingProfile>\n";
        $text .= "<ShippingProfileName>{$ShippingProfileName}</ShippingProfileName>\n";
        $text .= "</SellerShippingProfile>\n";
        $text .= "</SellerProfiles>\n";
        $text .= "<Site>US</Site>\n";
        if (!empty($sku)) {
            $text .= "<SKU>{$sku}</SKU>\n";
        }

        // if (array_key_exists("StoreCategory", $data) && !empty($data['StoreCategory'])) {
        //     $text .= "<Storefront>\n";
        //     if (array_key_exists("StoreCategory2", $data) && !empty($data['StoreCategory2'])) {
        //         $text .= "<StoreCategory2ID>{$data['StoreCategory2']}</StoreCategory2ID>\n";
        //     }
        //     $text .= "<StoreCategoryID>{$data['StoreCategory']}</StoreCategoryID>\n";
        //     $text .= "</Storefront>\n";
        // }



        $text .= "</Item>\n";
        $text .= "</AddFixedPriceItemRequest>";
        $xml = new \SimpleXMLElement($text);
        return $xml;
    }
    public function make_delete_item_xml($item)
    {

        $text = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
        <EndItemRequest xmlns=\"urn:ebay:apis:eBLBaseComponents\">
          <RequesterCredentials>
            <eBayAuthToken>" . config('app.ebay_token') . "</eBayAuthToken>
          </RequesterCredentials>
          <ItemID>{$item->ebay_id}</ItemID>
          <EndingReason>NotAvailable</EndingReason>
          <ErrorLanguage>en_US</ErrorLanguage>
          <WarningLevel>Low</WarningLevel>
        </EndItemRequest>";

        $xml = new \SimpleXMLElement($text);
        return $xml;
    }

    private function make_description_html($item, $site)
    {
        switch ($site) {
            case 'rakuten':
                $item_settings = $this->sites[$site]::find($item->rakuten_id);
                break;

            case 'digimart':
                $item_settings = $this->sites[$site]::find($item->digimart_id);
                break;

            case 'hardoff':
                $item_settings = $this->sites[$site]::find($item->hardoff_id);
                break;

            case 'secoundstreet':
                $item_settings = $this->sites[$site]::find($item->secoundstreet_id);
                break;

            default:
                # code...
                break;
        }
        $template = templates::find($item_settings->template);
        $slider = "";

        $html = $template->source;
        $title = $item->en_title;
        $description = nl2br($item->en_content);

        $html = str_replace(['##TITLE##', '##DESCRIPTION##'], [$title, $description], $html);
        $html = preg_replace('/\n/', '', $html);

        return $html;
    }

    private function ebay_regist_item($xml_data)
    {
        $http_headers = array(
            "Content-Type: text/xml",
            "X-EBAY-API-COMPATIBILITY-LEVEL: 967",
            "X-EBAY-API-CALL-NAME: AddFixedPriceItem",
            "X-EBAY-API-SITEID: 0",
            "X-EBAY-API-DEV-NAME: " . config('app.ebay_client_id'),
            "X-EBAY-API-APP-NAME: " . config('app.ebay_client_id'),
            "X-EBAY-API-CERT-NAME: " . config('app.ebay_client_id')
        );


        $xml = $xml_data->asXML();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        $_result = curl_exec($ch);
        $result = simplexml_load_string($_result);
        $result = json_encode($result);
        $result = json_decode($result, true);
        return $result;
    }
    public function ebay_delete_item($xml_data)
    {
        $http_headers = array(
            "Content-Type: text/xml",
            "X-EBAY-API-COMPATIBILITY-LEVEL: 967",
            "X-EBAY-API-CALL-NAME: EndItem",
            "X-EBAY-API-SITEID: 0",
            "X-EBAY-API-DEV-NAME: " . config('app.ebay_client_id'),
            "X-EBAY-API-APP-NAME: " . config('app.ebay_client_id'),
            "X-EBAY-API-CERT-NAME: " . config('app.ebay_client_id')
        );



        $xml = $xml_data->asXML();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        $_result = curl_exec($ch);
        $result = simplexml_load_string($_result);
        $result = json_encode($result);
        $result = json_decode($result, true);
        return $result;
    }

    public function get_items_detail($page)
    {
        // date_default_timezone_set('UTF');
        $start = date("Y-m-d", strtotime("-3 day"));
        $end = date("Y-m-d H:i:s");
        $text = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
					<GetSellerListRequest xmlns=\"urn:ebay:apis:eBLBaseComponents\">
					  <RequesterCredentials>
                      <eBayAuthToken>" . config('app.ebay_token') . "</eBayAuthToken>\n;
					  </RequesterCredentials>
					  <StartTimeFrom>{$start}</StartTimeFrom> 
  					  <StartTimeTo>{$end}</StartTimeTo> 
					  <ErrorLanguage>en_US</ErrorLanguage>
					  <WarningLevel>High</WarningLevel>
					  <GranularityLevel>Coarse</GranularityLevel> 
					  <IncludeWatchCount>true</IncludeWatchCount> 
					  <Pagination> 
                        <PageNumber>{$page}</PageNumber>
					    <EntriesPerPage>200</EntriesPerPage> 
					  </Pagination> 
					</GetSellerListRequest>";
        $http_headers = array(
            "Content-Type: text/xml",
            "X-EBAY-API-COMPATIBILITY-LEVEL: 967",
            "X-EBAY-API-CALL-NAME: GetSellerList",
            "X-EBAY-API-SITEID: 0",
            "X-EBAY-API-DEV-NAME: " . config('app.ebay_client_id'),
            "X-EBAY-API-APP-NAME: " . config('app.ebay_client_id'),
            "X-EBAY-API-CERT-NAME: " . config('app.ebay_client_id')
        );

        $xml = $text;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        $_result = curl_exec($ch);
        $result = simplexml_load_string($_result);
        $result = json_encode($result);
        $result = json_decode($result, true);
        return $result;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EbayItem  $ebayItem
     * @return \Illuminate\Http\Response
     */
    public function set_items_detail(EbayItem $ebayItem)
    {
        $data = [];
        for ($i = 1; $i <= 100; $i++) {
            $data = $this->get_items_detail($i);
            if (isset($data['ItemArray']['Item'])) {
                var_dump($data['ItemArray']['Item']);
                foreach ((array)$data['ItemArray']['Item'] as $value) {
                    $ebay_item = EbayItem::where('ebay_id', $value['ItemID'])->first();
                    if ($ebay_item) {
                        $ebay_item->image = $value['PictureDetails']['PictureURL'][0];
                        $ebay_item->view_url = $value['ListingDetails']['ViewItemURL'];
                        $ebay_item->save();
                    }
                }
            } else {
                break;
            }
        }
    }
}
