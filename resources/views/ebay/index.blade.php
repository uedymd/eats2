<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ebay設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="table-auto w-full mt-5">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2 w-1/12"></th>
                                <th class="border px-4 py-2 w-1/12">アイテム</th>
                                <th class="border px-4 py-2 w-3/12">タイトル</th>
                                <th class="border px-4 py-2 w-1/12">仕入元</th>
                                <th class="border px-4 py-2 w-1/12">販売価格</th>
                                <th class="border px-4 py-2">エラー</th>
                                <th class="border px-4 py-2 w-2/12">追跡</th>
                                <th class="border px-4 py-2 w-1/12">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ebay_items as $ebay_item)
                            <tr>
                                <td class="border px-4 py-2">
                                    {{$ebay_item->id}}
                                </td>
                                <td class="border px-4 py-2">
                                    @if($ebay_item->image!=='')
                                    <img src="{{$ebay_item->image}}" alt="" class="block" style="max-width:100%;height:auto;">
                                    @endif
                                    @if(!is_null($ebay_item->view_url))
                                    <a href="{{$ebay_item->view_url}}" target="_blank" class="block rounded bg-gray-500 p-2 text-white text-center mt-2">View</a>
                                    @else
                                     <small>詳細取得中</small>
                                    @endif
                                </td>
                                <td class="border px-4 py-2">
                                    {{$ebay_item->title}}
                                    @if($ebay_item->ebay_id>0)
                                    <br>【{{$ebay_item->ebay_id}}】
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    {{$ebay_item->site}}
                                    <a href="{{$suppliers[$ebay_item->id]}}" target="_blank" class="block rounded bg-gray-500 p-2 text-white text-center mt-2">View</a>
                                </td>
                                <td class="border px-4 py-2 text-right">${{number_format($ebay_item->price)}}
                                </td>
                                <td class="border px-4 py-2">
                                    <?php
                                    $errors = unserialize($ebay_item->error);
                                    if ($errors !== false) {
                                        foreach ($errors as $error) {
                                            echo $error;
                                            if ($error !== end($errors)) {
                                                echo "<br>";
                                            }
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="border px-4 py-2 text-right">{{$ebay_item->tracking_at}}</td>
                                <td class=" border px-4 py-2 text-center">
                                    <a href="{{ route('ebay.delete',['id'=>$ebay->id]) }}" class="block rounded bg-red-600 p-2 mt-2 text-white">出品取り消し</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-10">
                        {{ $ebay_items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>