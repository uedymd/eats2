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
                        <a href="{{ route('rakuten.create'); }}" class="inline-flex items-center justify-center w-10 h-10 mr-2 text-indigo-100 transition-colors duration-150 bg-indigo-700 rounded-lg focus:shadow-outline hover:bg-indigo-800">
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
                                <th class="border px-4 py-2">キーワード</th>
                                <th class="border px-4 py-2">ジャンル</th>
                                <th class="border px-4 py-2">除外キーワード</th>
                                <th class="border px-4 py-2">除外URL</th>
                                <th class="border px-4 py-2">最小価格</th>
                                <th class="border px-4 py-2">最大価格</th>
                                <th class="border px-4 py-2">レート</th>
                                <th class="border px-4 py-2">ステータス</th>
                                <th class="border px-4 py-2">最終チェック</th>
                                <th class="border px-4 py-2">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rakutens as $rakuten)
                            <tr>
                                <td class="border px-4 py-2"><a href="{{ route('rakuten.items',['id'=>$rakuten->id]) }}" class="inline-flex items-center justify-center w-10 h-10 mr-2 text-indigo-100 transition-colors duration-150 bg-indigo-700 rounded-lg focus:shadow-outline hover:bg-indigo-800 mt-5">{{$rakuten->id}}<a></td>
                                <td class="border px-4 py-2">{{$rakuten->title}}<br>（{{$items[$rakuten->id]}}件）</td>
                                <td class="border px-4 py-2">{{$rakuten->keyword}}</td>
                                <td class="border px-4 py-2">
                                    @if($rakuten->genre || $rakuten->genre_id)
                                    {{$rakuten->genre}}（{{$rakuten->genre_id}}）
                                    @endif
                                </td>
                                <td class="border px-4 py-2">{{$rakuten->ng_keyword}}</td>
                                <td class="border px-4 py-2">{{$rakuten->ng_url}}</td>
                                <td class="border px-4 py-2 text-right">
                                    @if($rakuten->price_min)
                                    {{number_format($rakuten->price_min)}}円
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-right">
                                    @if($rakuten->price_max)
                                    {{number_format($rakuten->price_max)}}円
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-right">
                                    @if($rakuten->rate)
                                    {{$rakuten->rate}}倍
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-right @if($rakuten->status==2){{ 'text-red-700' }} @endif">{{$status_array[$rakuten->status]}}</td>
                                <td class="border px-4 py-2 text-right">{{$rakuten->checked_at}}</td>
                                <td class=" border px-4 py-2 text-center">
                                    <a href="{{ route('rakuten.edit',['id'=>$rakuten->id]) }}" class="block rounded bg-blue-600 p-2 text-white">編集</a>
                                    <a href="{{ route('rakuten.delete',['id'=>$rakuten->id]) }}" class="block rounded bg-red-600 p-2 mt-2 text-white">削除</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <a href="{{ route('rakuten.create'); }}" class="inline-flex items-center justify-center w-10 h-10 mr-2 text-indigo-100 transition-colors duration-150 bg-indigo-700 rounded-lg focus:shadow-outline hover:bg-indigo-800 mt-5">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                            <path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" fill-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>