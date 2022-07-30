<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Models\Message;
use App\Models\EbayItem;
use App\Models\MessageReply;
use App\Models\User;
use App\Models\RakutenItem;
use App\Models\DigimartItems;
use App\Models\HardoffItems;
use App\Models\SecoundstreetItems;
use App\Http\Controllers\EbayItemController;
use KubAT\PhpSimple\HtmlDomParser;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Boolean;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MessageController extends Controller
{

    private $api_url = 'https://api.ebay.com/ws/api.dll';

    private $status_array = [''=>'ステータスを選択','1'=>'販売元問合せ中', '2'=>'確認中', '3'=>'返信済'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = $this->status_array;
        $messages = Message::orderByDesc('ReceiveDate')->paginate(150);

        return view('message/index', compact('messages','status'));
    }

    private function get_relation_member(){
        $users = [];
        $reply = MessageReply::
            join("users",'users.id','=','message_replies.member_id')
            ->orderByDesc("message_replies.created_at");

        if($reply->count()>0){
            $records = $reply->get();
            foreach($records as $record){
                $users[$record->member_id] = $record->name;
            }
            return $users;
        }
    }

    private function get_relation_items(){
        $ebay = EbayItem::join('messages','ebay_items.ebay_id','=','messages.ItemID');
        $items = [];
        if($ebay->count()>0){
            $records = $ebay->get();
            foreach($records as $record){
                $items[$record->id] = $record;
            }
            return $items;
        }
    }

    public function get_messages()
    {
        $this->set_text();
    }

    public function set_text()
    {
        $this->set_headers();
        $messages = Message::where('Text', null)->select('MessageID')->orderBy('ReceiveDate')->limit(10);
        while ($messages->count() > 0) {
            $texts = $this->get_message_text($messages->get());
            if ($texts['Ack'] == 'Success') {
                if (isset($texts['Messages']['Message']["MessageID"])) {
                    $record = Message::where('MessageID', $texts['Messages']['Message']["MessageID"])->first();
                    if ($record) {
                        $plainText = $this->parse_html($texts['Messages']['Message']["Text"]);
                        $record->Text = $plainText;
                        $record->save();
                    }
                } else {
                    foreach ((array)$texts['Messages']['Message'] as $text) {
                        $record = Message::where('MessageID', $text['MessageID'])->first();
                        if ($record) {
                            $plainText = $this->parse_html($text['Text']);
                            $record->Text = $plainText;
                            $record->save();
                        }
                    }
                }
            }
            $messages = Message::where('Text', null)->select('MessageID')->orderBy('ReceiveDate')->limit(10);
        }
    }

    private function parse_html($html)
    {
        $dom = HtmlDomParser::str_get_html($html);
        $returnHtml = '';
        $plainText = $dom->getElementById("UserInputtedText");
        $returnHtml .= $plainText->text();
        return $returnHtml;
    }


    public function set_headers()
    {
        $result = $this->get_message_headers();

        $messages = $result['Messages']['Message'];

        foreach ($messages as $message) {
            if (!empty($message['MessageType'])) {
                if (Message::where('MessageID', $message['MessageID'])->exists()) {
                    $record = Message::where('MessageID', $message['MessageID'])->first();
                } else {
                    $record = new Message();
                }
                if (isset($message['Sender'])) {
                    $record->Sender = $message['Sender'];
                }
                if (isset($message['SendingUserID'])) {
                    $record->SendingUserID = $message['SendingUserID'];
                }
                if (isset($message['RecipientUserID'])) {
                    $record->RecipientUserID = $message['RecipientUserID'];
                }
                if (isset($message['SendToName'])) {
                    $record->SendToName = $message['SendToName'];
                }
                if (isset($message['Subject'])) {
                    $record->Subject = $message['Subject'];
                }
                if (isset($message['MessageID'])) {
                    $record->MessageID = $message['MessageID'];
                }
                if (isset($message['ExternalMessageID'])) {
                    $record->ExternalMessageID = $message['ExternalMessageID'];
                }
                if (isset($message['ReceiveDate'])) {
                    $ReceiveDate = date('Y-m-d h:i:s', strtotime($message['ReceiveDate']));
                    $record->ReceiveDate = $ReceiveDate;
                }
                if (isset($message['ExpirationDate'])) {
                    $ExpirationDate = date('Y-m-d h:i:s', strtotime($message['ExpirationDate']));
                    $record->ExpirationDate = $ExpirationDate;
                }
                if (isset($message['ItemID'])) {
                    $record->ItemID = $message['ItemID'];
                }
                if (isset($message['Replied']) && !is_null($message['Replied'])) {
                    if ($message['Replied'] == "true") {
                        $replied = 1;
                    } else {
                        $replied = 0;
                    }
                    $record->Replied = $replied;
                }
                if (isset($message['ResponseDetails'])) {
                    $record->ResponseDetails = serialize($message['ResponseDetails']);
                }
                if (isset($message['MessageType'])) {
                    $record->MessageType = $message['MessageType'];
                }
                if (isset($message['ItemEndTime'])) {
                    $ItemEndTime = date('Y-m-d h:i:s', strtotime($message['ItemEndTime']));
                    $record->ItemEndTime = $ItemEndTime;
                }
                if (isset($message['ItemTitle'])) {
                    $record->ItemTitle = $message['ItemTitle'];
                }

                if (isset($message["MessageMedia"])) {
                    $record->MessageMedia = serialize($message['MessageMedia']);
                }
                $record->save();
            }

        }
    }

    private function get_message_headers()
    {
        $text = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n
        <GetMyMessagesRequest xmlns=\"urn:ebay:apis:eBLBaseComponents\">
          <RequesterCredentials>
            <eBayAuthToken>" . config('app.ebay_token') . "</eBayAuthToken>
          </RequesterCredentials>
          <WarningLevel>High</WarningLevel>
          <DetailLevel>ReturnHeaders</DetailLevel>
        </GetMyMessagesRequest>";
        $http_headers = array(
            "Content-Type: text/xml",
            "X-EBAY-API-COMPATIBILITY-LEVEL: 967",
            "X-EBAY-API-CALL-NAME: GetMyMessages",
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

    private function get_message_text($ids)
    {

        $idsTag = '';

        foreach ($ids as $id) {
            $_id = (int)$id->MessageID;
            $idsTag .= "<MessageID>{$_id}</MessageID>\n";
        }

        $text = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n
        <GetMyMessagesRequest xmlns=\"urn:ebay:apis:eBLBaseComponents\">\n
          <DetailLevel>ReturnMessages</DetailLevel>\n
          <RequesterCredentials>\n
          <eBayAuthToken>" . config('app.ebay_token') . "</eBayAuthToken>\n
          </RequesterCredentials>\n
          <MessageIDs>\n
            {$idsTag}
          </MessageIDs>\n
        </GetMyMessagesRequest>";
        $http_headers = array(
            "Content-Type: text/xml",
            "X-EBAY-API-COMPATIBILITY-LEVEL: 967",
            "X-EBAY-API-CALL-NAME: GetMyMessages",
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
     * @param  \App\Http\Requests\StoreMessageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMessageRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message, $id)
    {   
        $status = $this->status_array;
        $current = Message::find($id);
        $records = Message::where('Sender', $current->Sender)->where('ItemID', $current->ItemID)->orderBy('ReceiveDate')->get();
        $replies = [];
        $users = [];
        $messages = Message::orderByDesc('ReceiveDate')->paginate(150);
        foreach((array)$records as $record){
            if($record){
                foreach($records as $value){
                    $reply = MessageReply::where('message_replies.message_id',$value->id)
                    ->join('users','users.id','=','message_replies.member_id')
                    ->orderBy('message_replies.created_at');
                    if($reply->count() > 0){
                        $replies[$value->id] = $reply->get();
                    }
                }
            }
        }
        return view('message/show', compact('current', 'records','status','replies','messages'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMessageRequest  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMessageRequest $request, Message $message)
    {
        // dd($request);
    }


    public function send(Request $request)
    {

        $current = $request->current;
        $comment = $request->return;
        $itemID = $request->itemID;
        $sender = $request->sender;
        $parent = $request->parent;
        $status = $request->status;
        $images = $request->images;
        if(!empty($comment)){
            $result = $this->sent_message($comment, $itemID, $parent, $sender,$images);
            if ($result['Ack'] == 'Success') {
                $flush = 'メッセージを送信しました。';

                $reply = new MessageReply;
                $reply->message_id = $current;
                $reply->member_id = Auth::id();
                $reply->text = $comment;
                if(!empty($images)){
                    $reply->images = serialize($images);
                }
                $reply->save();
                $this->set_headers();
            } else {
                $flush = 'メッセージの送信に失敗しました。';
            }
            $request->session()->flash('messageResult', $flush);
        }

        if(!empty($status)){
            $record = Message::find($current);
            $record->status = $status;
            $record->save();
            $flush = 'ステータスを変更しました。';
            $request->session()->flash('mesasgeStatus', $flush);
        }

        return redirect("message/show/{$current}");
    }

    private function sent_message($comment, $itemID, $parent, $sender,$images)
    {
        $text = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n
        <AddMemberMessageRTQRequest xmlns=\"urn:ebay:apis:eBLBaseComponents\">\n
          <RequesterCredentials>\n
            <eBayAuthToken>" . config('app.ebay_token') . "</eBayAuthToken>\n
          </RequesterCredentials>\n
          <ItemID>{$itemID}</ItemID>\n
          <MemberMessage>\n
            <Body>\n
              {$comment}
            </Body>\n
            <DisplayToPublic>false</DisplayToPublic>\n
            <EmailCopyToSender>false</EmailCopyToSender>\n";
        
            if(!empty($images)){
                foreach((array)$images as $image){
                    $text .= "<MessageMedia>\n
                    <MediaName>image</MediaName>\n
                    <MediaURL>{$image}</MediaURL>\n
                    </MessageMedia>\n";
                }
            }

        $text .="<!-- This is the  unique identifier of the buyer's question. Message ID values can be retrieved with a GetMyMessages call -->\n
            <ParentMessageID>{$parent}</ParentMessageID>\n
            <!-- This is the user ID of the prospective buyer/bidder that asked the question -->\n
            <RecipientID>{$sender}</RecipientID>\n
          </MemberMessage>\n
        </AddMemberMessageRTQRequest>";
        $http_headers = array(
            "Content-Type: text/xml",
            "X-EBAY-API-COMPATIBILITY-LEVEL: 967",
            "X-EBAY-API-CALL-NAME: AddMemberMessageRTQ",
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

    public function upload(Request $request)
    {
        $img = $request->file('image')->store('images','public');
        $imageURL = "http://{$_SERVER['HTTP_HOST']}/storage/{$img}";
        $result = $this->image_upload($imageURL);
        return $result;
    }

    private function image_upload($image)
    {
        $text = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
        <UploadSiteHostedPicturesRequest xmlns=\"urn:ebay:apis:eBLBaseComponents\">
          <RequesterCredentials>
          <eBayAuthToken>" . config('app.ebay_token') . "</eBayAuthToken>\n
          </RequesterCredentials>
          <WarningLevel>High</WarningLevel>
          <ExternalPictureURL>{$image}</ExternalPictureURL>
          <PictureName></PictureName>
        </UploadSiteHostedPicturesRequest>";
        $http_headers = array(
            "Content-Type: text/xml",
            "X-EBAY-API-COMPATIBILITY-LEVEL: 967",
            "X-EBAY-API-CALL-NAME: UploadSiteHostedPictures",
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

    public function totalling(Request $request){

        if(!isset($request->year)||!isset($request->month)){
            $startDate = Carbon::createFromFormat('Y-m-d', date('Y-m-01'))->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', date('Y-m-t'))->endOfDay();
        }else{
            $startDate = Carbon::createFromFormat('Y-m-d', date("{$request->year}-{$request->month}-1"))->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', date("{$request->year}-{$request->month}-t"))->endOfDay();
        }

        $replies = MessageReply::select('users.name')
        ->selectRaw('COUNT(users.id) as count_userId')
        ->join('users','users.id','=','message_replies.member_id')
        ->whereBetween('message_replies.created_at', [$startDate, $endDate])
        ->groupBy("users.id")
        ->get();
        return view('message/totalling', compact('replies','startDate','endDate'));
    }

    public function get_side_items(Request $request){
        $ids = explode(',',$request[0]);
        $message = EbayItem::join('messages','ebay_items.ebay_id','=','messages.ItemID');
        
        foreach($ids as $id){
            $message->orWhere('messages.id',$id);
        }
        if($message->count() > 0){
            return json_encode($message->get());
        }
    }

    public function get_item_detail(Request $request)
    {
        $ebay = EbayItem::join('messages','ebay_items.ebay_id','=','messages.ItemID')
        ->where('messages.id',$request->id)->first();
        if($ebay){
            $ebayItem = new EbayItemController;
            $target = $ebayItem->models[$ebay->site]::find($ebay->supplier_id);
            $suppliers = '';
    
            switch ($ebay->site) {
                case 'rakuten':
                    $rakuten_item = RakutenItem::find($ebay->supplier_id);
                    if ($rakuten_item) {
                        $suppliers = $rakuten_item->url;
                    }
                    break;
                case 'digimart':
                    $digimart_item = DigimartItems::find($ebay->supplier_id);
                    if ($digimart_item) {
                        $suppliers = $digimart_item->url;
                    }
                    break;
    
                case 'hardoff':
                    $hardoff_item = HardoffItems::find($ebay->supplier_id);
                    if ($hardoff_item) {
                        $suppliers = $hardoff_item->url;
                    }
                    break;
    
                case 'secoundstreet':
                    $secoundstreet_item = SecoundstreetItems::find($ebay->supplier_id);
                    if ($secoundstreet_item) {
                        $suppliers = $secoundstreet_item->url;
                    }
                    break;
    
                default:
                    # code...
                    break;
            }
            $data = [
                'ebay'  => $ebay,
                'target'  => $target,
                'suppliers'  => $suppliers
            ];
            return json_encode($data);
        }
    }

    


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }
}
