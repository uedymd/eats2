<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            メッセージツール
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="max-w-screen-2xl px-4 md:px-8 mx-auto">
                        <div class="w-1/12">
                            <a href="{{ route('message.index') }}" class="text-sm block rounded bg-gray-500 p-2 mt-2 text-white text-center">一覧に戻る</a>
                        </div>

                        <div class="flex">
                            <div class="w-1/4 shrink-0 h-screen overflow-scroll">
                                <div class="mt-10">
                                    {{ $messages->links('vendor.pagination.tailwind2') }}
                                </div>
            
                                <div class="mt-5" >
                                    @foreach($messages as $message)
                                    @php
                                        $class = "bg-gray-100";
                                        $status_message = "";
                                        if($message->Replied||$message->status == 3){
                                            $class = " bg-gray-400";
                                        }
                                        if($message->status == 1){
                                            $class .= " bg-green-100";
                                        }
                                        if($message->status == 2){
                                            $class .= " bg-red-100";
                                        }
                                        if($message->id == $current->id){
                                            $class .= " border-2 border-indigo-600";
                                        }
                                        if(!is_null($message->status)){
                                            $status_message = $status[$message->status];
                                        }
                                    @endphp
                                    <div class="block__mail py-5 px-5 {{$class}} mb-1">
                                        <a href="{{ route('message.show',['id'=>$message->id]) }}" class="block">
                                            <div class="flex justify-between">
                                                @if(!empty($items[$message->id]) && !is_null(!empty($items[$message->id]->image)))
                                                <div class="w-3/12 shrink-0 mr-5">
                                                    <img src="{{$items[$message->id]->image}}" alt="">
                                                </div>
                                                @endif
                                                <div class="w-9/12">
                                                        <div class="block__sender text-sm text-blue-500">{{$message->Sender}}</div>
                                                        @if(!empty($status_message))
                                                        <div class="block__status text-sm">【{{$status_message}}】</div>
                                                        @endif
                                                        @if(!empty($items[$message->id]))
                                                            {{$items[$message->id]->title;}}
                                                        @endif
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                                </div>
            
                                <div class="mt-10">
                                    {{ $messages->links('vendor.pagination.tailwind2') }}
                                </div>
                            </div>
                            <div class="w-2/4 ml-5 shrinki-0 h-screen overflow-scroll">
                                @foreach ($records as $record)
                                @php
                                $class = "";
                                if($current->id == $record->id){
                                    $class .= " bg-gray-100";
                                }
                                @endphp
                                <div class="w-8/12 mt-5 max-w-screen-md text-gray-500 md:text-lg mb-5 px-2 py-2 {{$class}}">
                                    <p class="text-indigo-500 font-semibold text-sm">Sent : {{$record->ReceiveDate}}</p>
                                    <p class="text-indigo-500 font-semibold mb-1 md:mb-1 text-sm">From : {{$record->Sender}}</p></p>
                                    {!! $record->Text !!}
                                    @php
                                    $imageArray = [];
                                    if(!is_null($record->MessageMedia)){
                                        $images = unserialize($record->MessageMedia);
                                        if(isset($images["MediaURL"])){
                                            $imageArray[] = $images["MediaURL"];
                                        }else{
                                            foreach($images as $image){
                                                $imageArray[] = $image["MediaURL"];
                                            }
                                        }
                                        echo "<div class=\"flex mt-3\">";
                                            foreach ($imageArray as $value) {
                                                echo "<div class=\"w-2/12 mr-2\"><a href=\"{$value}\" rel=\"lightbox[]\"><img src=\"{$value}\"></a></div>";
                                            }
                                        echo "</div>";
                                    }
                                    @endphp
                                </div>
                                @if(!empty($replies[$record->id]))
                                    <div class="flex flex-row-reverse">
                                        <div class="w-8/12 mt-5 max-w-screen-md text-gray-500 md:text-lg mb-5 px-2 py-2 bg-blue-100">
                                            @foreach($replies[$record->id] as $reply)
                                            <div class="mb-3">
                                                <p class="text-indigo-500 font-semibold text-sm">Sent : {{$reply->created_at}}</p>
                                                <p class="text-indigo-500 font-semibold mb-1 md:mb-1 text-sm">From : {{$users[$reply->member_id]}}</p></p>
                                                    {!! nl2br($reply->text) !!}
                                                    @if(!is_null($reply->images))
                                                        @php 
                                                        $images = unserialize($reply->images);
                                                        @endphp
                                                        <div class="flex">
                                                            @foreach($images as $image)
                                                            <div class="w-2/12 mr-2 mb-3"><a href="{{$image}}" rel="lightbox[]"><img src="{{$image}}" alt=""></a></div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                @endforeach
                                <div class="px-5 py-5">
                                    @if (session('messageResult'))
                                    <div class="flex items-center bg-blue-500 text-white text-sm font-bold px-4 py-3 mb-3" role="alert">
                                        <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z" />
                                        </svg>
                                        <p>{{ session('messageResult') }}</p>
                                    </div>
                                    @endif
                                    @if (session('mesasgeStatus'))
                                    <div class="flex items-center bg-blue-500 text-white text-sm font-bold px-4 py-3" role="alert">
                                        <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z" />
                                        </svg>
                                        <p>{{ session('mesasgeStatus') }}</p>
                                    </div>
                                    @endif
                                    <div class="flex">
                                        <div class="imageUpload mb-3">
                                            <input type="file" name="uploader">
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('message.send'); }}">
                                        <div>
                                            <div class="flex imageUploads">
    
                                            </div>
                                            <div>
                                                @csrf
                                                {{Form::textarea('return', '', ['class' => 'form-control block w-full', 'id' => 'return', 'rows' => '10'])}}
                                                {{Form::hidden('current', $current->id)}}
                                                {{Form::hidden('itemID', $current->ItemID)}}
                                                {{Form::hidden('sender', $current->Sender)}}
                                                {{Form::hidden('parent', $current->ExternalMessageID)}}
                                                {{Form::select('status', $status,$current->status,['class' => 'form-control block mt-2'])}}
                                            </div>
                                        </div>
                                        <x-button class="mt-4">
                                            {{ __('返信') }}
                                        </x-button>
                                    </form>
                                </div>
                            </div>
                            <div class="w-1/4 ml-5 h-screen overflow-scroll">
                                @if(!empty($ebay))
                                    <div class="flex">
                                        <div class="w-3/12 shurink-0 mr-5">
                                            @if($ebay->image!=='')
                                            <img src="{{$ebay->image}}" alt="" class="block" style="max-width:100%;height:auto;">
                                            @endif
                                            @if(!is_null($ebay->view_url))
                                            <a href="{{$ebay->view_url}}" target="_blank" class="block rounded bg-gray-500 p-2 text-white text-center mt-2">View</a>
                                            @else
                                            <small>詳細取得中</small>
                                            @endif
                                        </div>
                                        <div class="w-9/12">
                                            {{$ebay->title}}
                                            @if($ebay->ebay_id>0)
                                            <br>【{{$ebay->ebay_id}}】
                                            @endif
                                            @if(isset($suppliers[$ebay->id]))
                                            <div class="w-8/12">
                                                <a href="{{$suppliers[$ebay->id]}}" target="_blank" class="block rounded bg-gray-500 p-2 text-white text-center mt-5">{{$ebay->site}}</a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex mt-5">
                                        <div class="w-8/12 mr-5 mt-2">
                                            {{$ebay->tracking_at}}
                                        </div>
                                        <div class="w-4/12 text-center">
                                            <a href="{{ route('ebay.delete',['id'=>$ebay->id]) }}" class="block rounded bg-red-600 p-2 text-white text-center">出品取消</a>
                                        </div>
                                    </div>
                                    <div class="block__translate mt-10">
                                        <div class="border-t-2 px-4 py-4">{!!nl2br($target->jp_content)!!}</div>
                                        <div class="border-t-2 px-4 py-4">{!!nl2br($target->en_content)!!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="loader" style="position:fixed;background:rgba(0,0,0,0.8);left:0;top:0;width:100%;height:100%;display:none;"></div>

</x-app-layout>