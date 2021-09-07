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
                                <th class="border px-4 py-2">タイトル</th>
                                <th class="border px-4 py-2">元サイト</th>
                                <th class="border px-4 py-2">販売価格</th>
                                <th class="border px-4 py-2">エラー</th>
                                <th class="border px-4 py-2">仕入元チェック</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ebay_items as $ebay_items)
                            <tr>
                                <td class="border px-4 py-2">
                                    {{$ebay_items->title}}
                                    @if($ebay_items->image!=='')
                                    <img src="{{$ebay_items->image}}" alt="" class="block" style="max-width:200px;height:auto;">
                                    @endif
                                    @if($ebay_items->ebay_id>0)
                                    {{$ebay_items->ebay_id}}
                                    @endif
                                </td>
                                <td class="border px-4 py-2">{{$ebay_items->site}}
                                <td class="border px-4 py-2">${{$ebay_items->price}}
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
                                <td class="border px-4 py-2">{{$ebay_items->supllier_checked_id}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>