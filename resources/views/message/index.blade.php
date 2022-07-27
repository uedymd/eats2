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
                    <div class="mt-10">
                        {{ $messages->links() }}
                    </div>

                    <div class="w-1/4 mt-5" >
                    @foreach($messages as $message)
                        @php
                            $class = "bg-gray-100";
                            $status_message = "";
                            if($message->Replied){
                                $class = "bg-gray-400";
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
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>