<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Models\Message;
use App\Models\EbayItem;
use KubAT\PhpSimple\HtmlDomParser;
use Illuminate\Http\Request;

class MessageController extends Controller
{

    private $api_url = 'https://api.ebay.com/ws/api.dll';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $this->set_text();
        $messages = Message::orderByDesc('ReceiveDate')->paginate(150);
        return view('message/index', compact('messages'));
    }

    public function set_text()
    {
        $this->set_headers();
        $messages = Message::where('Text', null)->select('MessageID')->orderBy('ReceiveDate')->limit(10)->get();
        if ($messages) {
            $texts = $this->get_message_text($messages);
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
        }
    }

    private function parse_html($html)
    {
        $dom = HtmlDomParser::str_get_html($html);
        $plainText = $dom->getElementById("UserInputtedText");
        return $plainText->text();
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
            }

            $record->save();
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
        $current = Message::find($id);
        $records = Message::where('Sender', $current->Sender)->where('ItemID', $current->ItemID)->orderByDesc('ReceiveDate')->get();
        $item = EbayItem::select('id', 'image')->where('ebay_id', (int)$current->ItemID)->first();
        return view('message/show', compact('current', 'records', 'item'));
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
        $result = $this->sent_message($comment, $itemID, $parent, $sender);
        if ($result['Ack'] == 'Success') {
            $flush = 'メッセージを送信しました。';
        } else {
            $flush = 'メッセージの送信に失敗しました。';
        }
        $request->session()->flash('status', $flush);
        return redirect("message/show/{$current}");
    }


    private function sent_message($comment, $itemID, $parent, $sender)
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
            <EmailCopyToSender>true</EmailCopyToSender>\n
            <!-- This is the  unique identifier of the buyer's question. Message ID values can be retrieved with a GetMyMessages call -->\n
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
