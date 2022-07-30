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
                        <div class="mt-10">
                            {{ $messages->links('vendor.pagination.tailwind2') }}
                        </div>

                        <div class="w-1/4 mt-5" >
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
                                    if(!is_null($message->status)){
                                        $status_message = $status[$message->status];
                                    }
                                @endphp
                                <div class="block__mail {{$class}} mb-1">
                                    <a href="{{ route('message.show',['id'=>$message->id]) }}" data-item="{{$message->id}}" class="block py-5 px-5">
                                        <div class="flex justify-between">
                                            <div class="w-9/12 block__data">
                                                    <div class="block__sender text-sm text-blue-500">{{$message->Sender}}</div>
                                                    @if(!empty($status_message))
                                                    <div class="block__status text-sm">【{{$status_message}}】</div>
                                                    @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-10">
                            {{ $messages->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>