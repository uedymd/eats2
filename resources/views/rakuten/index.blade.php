<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            楽天設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between">
                        <a href="{{ route('rakuten.create'); }}" class="inline-flex items-center justify-center w-10 h-10 mr-2 text-white transition-colors duration-150 bg-gray-700 rounded-lg focus:shadow-outline hover:bg-gray-800">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" fill-rule="evenodd"></path>
                            </svg>
                        </a>
                        <a href="{{ route('setting.edit',['site'=>'rakuten']) }}" class="block rounded bg-blue-600 p-2 text-white">共通設定</a>
                    </div>
                    <table class="table-auto w-full mt-5">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">ID</th>
                                <th class="border px-4 py-2">タイトル</th>
                                <th class="border px-4 py-2">検索設定</th>
                                <th class="border px-4 py-2">ジャンル</th>
                                <th class="border px-4 py-2">価格設定</th>
                                <th class="border px-4 py-2">ステータス</th>
                                <th class="border px-4 py-2">更新日</th>
                                <th class="border px-4 py-2">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rakutens as $rakuten)
                            <tr>
                                <td class="border px-4 py-2"><a href="{{ route('rakuten.items',$rakuten->id) }}" class="inline-flex items-center justify-center w-10 h-10 mr-2 text-indigo-100 transition-colors duration-150 bg-indigo-700 rounded-lg focus:shadow-outline hover:bg-indigo-800 mt-5">{{$rakuten->id}}<a></td>
                                    <td class="border px-4 py-2">
                                        {{$rakuten->title}}<br>（対象：{{$items[$rakuten->id]['count']}}件）
                                        <a href="{{ route('api.rakuten.search') }}" class="block rounded bg-blue-600 p-2 text-white text-center mt-5">即時反映</a><br>
                                        ・コンテンツ取得待ち：{{$items[$rakuten->id]['jp_content']}}件<br>
                                        ・タイトル翻訳待ち：{{$items[$rakuten->id]['en_title']}}件<br>
                                        ・コンテンツ翻訳待ち：{{$items[$rakuten->id]['en_content']}}件<br>
                                        ・ドル変換待ち：{{$items[$rakuten->id]['doller']}}件
                                </td>
                                <td class="border px-4 py-2">
                                    <span class="text-gray-500">検索キーワード：</span><br>
                                    {{$rakuten->keyword}}
                                    @if(!empty($rakuten->ng_keyword))
                                    <hr class="block my-2"><span class="text-gray-500">除外キーワード：</span><br>{{$rakuten->ng_keyword}}
                                    @endif
                                    @if(!empty($rakuten->ng_url))
                                    <hr class="block my-2"><span class="text-gray-500">除外URL：</span><br>{{$rakuten->ng_url}}
                                    @endif
                                    @if($rakuten->brand_set_name)
                                    <hr class="block my-2">
                                    <div class="mt-2"><span class="text-gray-500">対象ブランド設定：</span><br><a href="{{route('setting.brandset.edit',$rakuten->brand_set_id)}}" class="underline" target="_blank">{{$rakuten->brand_set_name}}</a></div>
                                    @endif
                                </td>
                                <td class="border px-4 py-2">
                                    @if($rakuten->genre || $rakuten->genre_id)
                                    {{$rakuten->genre}}<br>（{{$rakuten->genre_id}}）
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-right">
                                    @if($rakuten->price_min)
                                    {{number_format($rakuten->price_min)}}円
                                    @endif
                                    @if($rakuten->price_min||$rakuten->price_max)
                                    〜
                                    @endif
                                    @if($rakuten->price_max)
                                    {{number_format($rakuten->price_max)}}円
                                    @endif
                                    @if($rakuten->rate_set_name)
                                    <hr class="block my-2">
                                    <div class="mt-2"><span class="text-gray-500">価格レート設定：</span><a href="{{route('setting.rateset.edit',$rakuten->rate_set_id)}}" class="underline" target="_blank">{{$rakuten->rate_set_name}}</a></div>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-right @if($rakuten->status==2){{ 'text-red-700' }} @endif">{{$status_array[$rakuten->status]}}</td>
                                <td class="border px-4 py-2 text-right">{{$rakuten->updated_at}}</td>
                                <td class=" border px-4 py-2 text-center">
                                    <a href="{{ route('rakuten.edit',['id'=>$rakuten->id]) }}" class="block rounded bg-blue-600 p-2 text-white">編集</a>
                                    <a href="{{ route('rakuten.delete',['id'=>$rakuten->id]) }}" class="block rounded bg-red-600 p-2 mt-2 text-white">削除</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <a href="{{ route('rakuten.create'); }}" class="inline-flex items-center justify-center w-10 h-10 mr-2 text-white transition-colors duration-150 bg-gray-700 rounded-lg focus:shadow-outline hover:bg-gray-800 mt-5">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                            <path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" fill-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>