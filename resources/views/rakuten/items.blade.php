<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            楽天設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-scroll shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="table-auto w-full mt-5 bg-white">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">設定</th>
                                <th class="border px-4 py-2">URL</th>
                                <th class="border px-4 py-2">タイトル（JP）</th>
                                <th class="border px-4 py-2">コンテンツ（JP）</th>
                                <th class="border px-4 py-2">タイトル（EN）</th>
                                <th class="border px-4 py-2">コンテンツ（EN）</th>
                                <th class="border px-4 py-2">価格</th>
                                <th class="border px-4 py-2">画像</th>
                                <th class="border px-4 py-2">登録</th>
                                <th class="border px-4 py-2">更新</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                            <tr>
                                <td class="border px-4 py-2">{{$rakutebn_data->title}}</td>
                                <td class="border px-4 py-2"><a href="{{$item->url}}" target="_blank">{{$item->url}}</a></td>
                                <td class="border px-4 py-2">{{$item->jp_title}}</td>
                                <td class="border px-4 py-2">{{$item->jp_content}}</td>
                                <td class="border px-4 py-2">{{$item->en_title}}</td>
                                <td class="border px-4 py-2">{{$item->en_content}}</td>
                                <td class="border px-4 py-2">{{$item->price}}</td>
                                <td class="border px-4 py-2">
                                    @php
                                    $images = unserialize($item->images);
                                    @endphp
                                    @foreach((array)$images as $image)
                                    <img src="{{$image}}" class="block">
                                    @endforeach
                                </td>
                                <td class="border px-4 py-2 text-right">{{$item->created_at}}</td>
                                <td class="border px-4 py-2 text-right">{{$item->updated_at}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-10">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>