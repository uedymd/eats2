<?php

namespace App\Http\Controllers;

use App\Models\EbayItem;
use Illuminate\Http\Request;
use App\Models\Rakuten;
use App\Models\RakutenItem;

class EbayItemController extends Controller
{


    private $url = 'https://api.ebay.com/ws/api.dll';
    // private $url = 'https://api.sandbox.ebay.com/ws/api.dll';


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ebay_items = EbayItem::all();
        return view('ebay/index', compact('ebay_items'));
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
    public function show(EbayItem $ebayItem)
    {
        //
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
    public function destroy(EbayItem $ebayItem)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EbayItem  $ebayItem
     * @return \Illuminate\Http\Response
     */
    public function add(EbayItem $ebayItem, $id, $site)
    {
        switch ($site) {
            case 'rakuten':
                $item = RakutenItem::find($id);
                break;

            default:
                # code...
                break;
        }

        $xml = $this->make_add_item_xml($item);
        $register = $this->ebay_regist_item($xml);
        $ebay_item = new EbayItem();
        if ($register['Ack'] !== 'Failure') {
            $ebay_item->ebay_id = $register['ItemID'];
            $ebay_item->site = $site;
            $ebay_item->supplier_id = $id;
            $ebay_item->title = mb_strimwidth($item->en_title, 0, 70);
            $ebay_item->price = $item->doller;
            $images = unserialize($item->images);
            $ebay_item->image = $images[0];
            $ebay_item->save();
        } else {
            $ebay_item->ebay_id = 0;
            $ebay_item->site = $site;
            $ebay_item->supplier_id = $id;
            $ebay_item->title = mb_strimwidth($item->en_title, 0, 70);
            $ebay_item->price = $item->doller;
            $images = unserialize($item->images);
            $ebay_item->image = $images[0];
            $ebay_item->error = serialize($register['Errors']);
            $ebay_item->save();
        }
    }


    private function make_add_item_xml($item)
    {

        $item_settings = Rakuten::find($item->rakuten_id);
        $condtionID = 3000;
        $category = 33034;
        $Duration = 'GTC';
        $MinimumBestOfferPrice = '';
        $location = 'OSAKA';
        $Quantity = 1;
        $PaymentProfileName = "PayPal";
        $ReturnProfileName = "30Days";
        $ShippingProfileName = "guitar2";
        $type = "Electric Guitar";
        $brand = "Fender Japan";

        $title = mb_strimwidth($item->en_title, 0, 70);

        $text = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $text .= "<AddFixedPriceItemRequest xmlns=\"urn:ebay:apis:eBLBaseComponents\">\n";
        $text .= "<RequesterCredentials>\n";
        $text .= "<eBayAuthToken>{config('app.ebay_token')}</eBayAuthToken>\n";
        $text .= "</RequesterCredentials>\n";
        $text .= "<ErrorLanguage>en_US</ErrorLanguage>\n";
        $text .= "<Item>\n";
        $item_title = htmlspecialchars($title);
        $text .= "<Title>EN {$item_title}</Title>\n";
        $description = $this->make_description_html($item);
        $text .= "<Description><![CDATA[" . $description . "]]></Description>\n";
        $text .= "<PrimaryCategory><CategoryID>{$category}</CategoryID></PrimaryCategory>\n";
        $text .= "<StartPrice>{$item->doller}</StartPrice>\n";
        $text .= "<AutoPay>0</AutoPay>\n";
        $text .= "<CategoryMappingAllowed>true</CategoryMappingAllowed>\n";
        $text .= "<ConditionID>{$condtionID}</ConditionID>\n";
        $text .= "<Country>JP</Country>\n";
        $text .= "<Currency>USD</Currency>\n";
        $text .= "<DispatchTimeMax>5</DispatchTimeMax>\n";
        // if (array_key_exists("CustomLabel", $data) && !empty($data['CustomLabel'])) {
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
        foreach ($imgaes as $picture) {
            if (!empty($picture)) {
                $text .= "<PictureURL>{$picture}</PictureURL>\n";
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
        // if (array_key_exists("CustomLabel", $data) && !empty($data['CustomLabel'])) {
        //     $text .= "<SKU>{$data['CustomLabel']}</SKU>\n";
        // }

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
        $test_xml = new \SimpleXMLElement($text);
        return $test_xml;
    }

    private function make_description_html($item)
    {

        $images = unserialize($item->images);
        $slider = "";

        // foreach ((array)$images as $image) {
        //     $slider .= "<div class=\"image-gallery-slider\">
        //     <input name=\"image-swap\" id=\"image1\" type=\"radio\" checked=\"checked\">
        //     <label for=\"image1\">
        //         <img src=\"{$image}\" width=\"50\" alt=\"image\">
        //     </label>
        //     <div class=\"image-thumbnail\">
        //         <img src=\"{$image}\" alt=\"image\">
        //     </div>
        // </div>";
        // }

        $html = "<head>
        <meta charset=\"utf-8\">
        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
        <title>{$item->en_title}</title>
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
        <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css\">
        <link href=\"https://fonts.googleapis.com/css?family=Lato:100,200,300,400,500,600,700,800,900\" rel=\"stylesheet\">
        <link rel=\"stylesheet\" type=\"text/css\" href=\"https://tricraft.net/template/style-eg.css\">
    </head>
    
    <body><input class=\"toggle-nav\" id=\"toggle-nav\" type=\"checkbox\">
        <div class=\"mobile-bar\"><label for=\"toggle-nav\"></label></div>
        <div class=\"container\">
            </header>
            <div class=\"container-fluid content\">
                <div class=\"row\">
                    <div class=\"col-md-9 col-md-push-3\">
                        <div class=\"listing-title\">
                            <h1>{$item->en_title}</h1>
                        </div>
                        <div class=\"image-gallery-frame\" style=\"font-size: 14pt;\">
                            <div class=\"image-gallery\">
                                {$slider}
                            </div>
                        </div>
                        <div class=\"panel panel-default\" style=\"font-size: 14pt;\">
                            <div class=\"panel-heading\">
                                <h4 class=\"panel-title\"><i class=\"fa fa-file\" aria-hidden=\"true\"></i> <b>Item
                                        Description</b></h4>
                            </div>
                            <div class=\"panel-body\">
                                <p>{$item->en_content}</p>
                            </div>
                        </div>
                        <div class=\"panel panel-default\" style=\"\">
                            <div class=\"panel-heading\" style=\"font-size: 14pt;\">
                                <h4 class=\"panel-title\"><i class=\"fa fa-truck\" aria-hidden=\"true\"></i> <b>Shipping</b></h4>
                            </div>
                            <div class=\"panel-body\" style=\"\">
                                <p style=\"\">
                                    <font size=\"3\">Shipping by EMS, FedEx or DHL(international express mail service, with
                                        Tracking and Insurance, handling cost).&nbsp;<br>EMS takes 3-7 days, FedEx, DHL
                                        takes 2-4 days.<br>We will ship your item within 5 business days with carefully
                                        packed.&nbsp;<br></font>
                                </p>
                            </div>
                        </div>
                        <div class=\"panel panel-default\" style=\"\">
                            <div class=\"panel-heading\" style=\"font-size: 14pt;\">
                                <h4 class=\"panel-title\"><i class=\"fa fa-repeat\" aria-hidden=\"true\"></i> <b>Returns</b></h4>
                            </div>
                            <div class=\"panel-body\" style=\"\">
                                <p style=\"font-size: 14pt;\"><u>Money back guarantee within 30 days.</u>&nbsp;</p>
                                <p style=\"\">
                                    <font size=\"3\">We offer 30 days money back guarantee for defective products.<br>The
                                        buyer must pay for returning it back to us.<br>When there were defects in a
                                        instrument, please contact us.</font>
                                </p>
                            </div>
                            <font size=\"3\"></font>
                        </div>
                        <font size=\"3\">
                            <div class=\"panel panel-default\" style=\"\">
                                <div class=\"panel-heading\" style=\"font-size: 14pt;\">
                                    <h4 class=\"panel-title\"><i class=\"fa fa-tag\" aria-hidden=\"true\"></i> <b>International
                                            Buyers - Please Note:</b></h4>
                                </div>
                                <div class=\"panel-body\" style=\"\">
                                    <p style=\"\">
                                        <font size=\"3\">Import duties, taxes and charges are not included in the item price
                                            or shipping charges. These charges are the buyer's responsibility.<br>Please
                                            check with your country's customs office to determine what these additional
                                            costs will be prior to bidding/buying.<br>These charges are normally collected
                                            by the delivering freight (shipping) company or when you pick the item up - do
                                            not confuse them for additional shipping charges.<br>We do not mark merchandise
                                            values below value or mark items as 窶徃ifts窶 - US and International government
                                            regulations prohibit such behavior.</font>
                                    </p>
                                </div>
                            </div>
                            <div class=\"panel panel-default\" style=\"\">
                                <div class=\"panel-heading\" style=\"font-size: 14pt;\">
                                    <h4 class=\"panel-title\"><i class=\"fa fa-tag\" aria-hidden=\"true\"></i> <b>About Us</b>
                                    </h4>
                                </div>
                                <div class=\"panel-body\" style=\"\">
                                    <p style=\"font-size: 14pt;\"><u>If you have any problem, please contact us.</u>&nbsp;
                                        Your satisfaction is important to us.&nbsp;</p>
                                    <p style=\"\">
                                        <font size=\"3\">We have results that I sold of tens of thousands of by Internet
                                            sale.<br>We have pride ourselves in the Quality of items we offer. </font>
                                    </p>
                                    <p style=\"font-size: 14pt;\"><span style=\"font-size: 14pt;\"><u>All our items has been
                                                checked by technician.</u>&nbsp;</span></p>
                                    <p style=\"\">
                                        <font size=\"3\">As every seller dealing with pre-owned branded items in Japan we have
                                            as well license from local government to offer services of this
                                            kind.&nbsp;<br>We are officially registered as legal business and we have to
                                            comply with all rules and regulations including tax requirements from the
                                            Japanese government.</font>
                                    </p>
                                </div>
                            </div>
                        </font>
                    </div><!-- right column ends -->
                </div>
            </div>
        </div>";
        return $html;
    }

    private function ebay_regist_item($xml_data)
    {
        $http_headers = array(
            "Content-Type: text/xml",
            "X-EBAY-API-COMPATIBILITY-LEVEL: 967",
            "X-EBAY-API-CALL-NAME: AddFixedPriceItem",
            "X-EBAY-API-SITEID: 0",
            "X-EBAY-API-DEV-NAME: {config('app.ebay_client_id')}",
            "X-EBAY-API-APP-NAME: {config('app.ebay_client_id')}",
            "X-EBAY-API-CERT-NAME: {config('app.ebay_client_id')}"
        );

        $xml = $xml_data->asXML();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
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
}
