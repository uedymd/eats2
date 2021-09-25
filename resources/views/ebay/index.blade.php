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
                                <th class="border px-4 py-2 w-1/12">アイテム</th>
                                <th class="border px-4 py-2 w-3/12">タイトル</th>
                                <th class="border px-4 py-2 w-1/12">元サイト</th>
                                <th class="border px-4 py-2">販売価格</th>
                                <th class="border px-4 py-2">エラー</th>
                                <th class="border px-4 py-2">仕入元チェック</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ebay_items as $ebay_items)
                            <tr>
                                <td class="border px-4 py-2">
                                    @if($ebay_items->image!=='')
                                    <img src="{{$ebay_items->image}}" alt="" class="block" style="max-width:100%;height:auto;">
                                    @endif
                                    @if(!is_null($ebay_items->view_url))
                                    <a href="{{$ebay_items->view_url}}" target="_blank" class="block rounded bg-gray-500 p-2 text-white text-center mt-2">View</a>
                                    @endif
                                </td>
                                <td class="border px-4 py-2">
                                    {{$ebay_items->title}}
                                    @if($ebay_items->ebay_id>0)
                                    {{$ebay_items->ebay_id}}
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    {{$ebay_items->site}}
                                    <a href="{{$suppliers[$ebay_items->id]}}" target="_blank" class="block rounded bg-gray-500 p-2 text-white text-center mt-2">View</a>
                                </td>
                                <td class="border px-4 py-2 text-right">${{number_format($ebay_items->price)}}
                                </td>
                                <td class="border px-4 py-2">
                                    <?php
                                    $errors = unserialize($ebay_items->error);
                                    if ($errors !== false) {
                                        foreach ($errors as $error) {
                                            echo $error['LongMessage'];
                                            if ($error !== end($errors)) {
                                                echo "<br>";
                                            }
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="border px-4 py-2">{{$ebay_items->tracking_at}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>