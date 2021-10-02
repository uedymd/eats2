<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            デジマート設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between">
                        <a href="{{ route('digimart.create'); }}" class="inline-flex items-center justify-center w-10 h-10 mr-2 text-white transition-colors duration-150 bg-gray-700 rounded-lg focus:shadow-outline hover:bg-gray-800">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" fill-rule="evenodd"></path>
                            </svg>
                        </a>
                        <div class="flex">
                            <a href="{{ route('digimart.recheck') }}" class="block rounded bg-green-600 p-2 text-white text-center mr-3">再反映</a><br>
                            <a href="{{ route('setting.edit',['site'=>'digimart']) }}" class="block rounded bg-blue-600 p-2 text-white">共通設定</a>
                        </div>
                    </div>
                    <table class="table-auto w-full mt-5">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2"></th>
                                <th class="border px-4 py-2">タイトル</th>
                                <th class="border px-4 py-2 w-3/12">検索設定</th>
                                <th class="border px-4 py-2">価格設定</th>
                                <th class="border px-4 py-2">ステータス</th>
                                <th class="border px-4 py-2">更新日</th>
                                <th class="border px-4 py-2">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $cnt = 1; @endphp
                            @foreach($digimarts as $digimart)
                            <tr>
                                <td class="border px-4 py-2"><a href="{{ route('digimart.items',$digimart->id) }}" class="inline-flex items-center justify-center w-10 h-10 mr-2 text-indigo-100 transition-colors duration-150 bg-indigo-700 rounded-lg focus:shadow-outline hover:bg-indigo-800 mt-5">{{$cnt}}<a></td>
                                    <td class="border px-4 py-2">
                                        {{$digimart->title}}<br>（対象：{{$items[$digimart->id]['count']}}件）
                                        @php 
                                        
                                        if($items[$digimart->id]['count']>0){
                                            $jp_content_progress = round(100-($items[$digimart->id]['jp_content']/$items[$digimart->id]['count'])*100,1);
                                            $en_title_progress = round(100-($items[$digimart->id]['en_title']/$items[$digimart->id]['count'])*100,1);
                                            $en_brand_progress = round(100-($items[$digimart->id]['en_brand']/$items[$digimart->id]['count'])*100,1);
                                            $en_content_progress = round(100-($items[$digimart->id]['en_content']/$items[$digimart->id]['count'])*100,1);
                                            $doller_progress = round(100-($items[$digimart->id]['doller']/$items[$digimart->id]['count'])*100,1);
                                        }else{
                                            $jp_content_progress = "-";
                                            $en_title_progress = "-";
                                            $en_content_progress = "-";
                                            $en_brand_progress = "-";
                                            $doller_progress = "-";
                                        }

                                        @endphp
                                        <a href="{{ route('api.digimart.search',['id'=>$digimart->id]) }}" class="block rounded bg-blue-600 p-2 text-white text-center mt-5">即時検索</a>
                                        <div class="mt-5">
                                            コンテンツ取得待ち：<div class="text-right">{{$items[$digimart->id]['jp_content']}}件（進捗{{$jp_content_progress}}%）</div>
                                            タイトル翻訳待ち：<div class="text-right">{{$items[$digimart->id]['en_title']}}件（進捗{{$en_title_progress}}%）</div>
                                            ブランド翻訳待ち：<div class="text-right">{{$items[$digimart->id]['en_brand']}}件（進捗{{$en_brand_progress}}%）</div>
                                            コンテンツ翻訳待ち：<div class="text-right">{{$items[$digimart->id]['en_content']}}件（進捗{{$en_content_progress}}%）</div>
                                            ドル変換待ち：<div class="text-right">{{$items[$digimart->id]['doller']}}件（進捗{{$doller_progress}}%）</div>
                                        </div>
                                </td>
                                <td class="border px-4 py-2">
                                    <span class="text-gray-500">検索URL：</span><a href="{{$digimart->url}}" target="_blank">確認する</a>
                                    @if(!empty($digimart->ng_keyword))
                                    <hr class="block my-2"><span class="text-gray-500">除外キーワード：</span><br>{{$digimart->ng_keyword}}
                                    @endif
                                    @if(!empty($digimart->ng_url))
                                    <hr class="block my-2"><span class="text-gray-500">除外URL：</span><br>{{$digimart->ng_url}}
                                    @endif
                                    @if($digimart->brand_set_name)
                                    <hr class="block my-2">
                                    <div class="mt-2"><span class="text-gray-500">対象ブランド設定：</span><br><a href="{{route('setting.brandset.edit',$digimart->brand_set_id)}}" class="underline" target="_blank">{{$digimart->brand_set_name}}</a></div>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-right">
                                    @if($digimart->rate_set_name)
                                    <div class="mt-2"><span class="text-gray-500">価格レート設定：</span><br><a href="{{route('setting.rateset.edit',$digimart->rate_set_id)}}" class="underline" target="_blank">{{$digimart->rate_set_name}}</a></div>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-right @if($digimart->status==2){{ 'text-red-700' }} @endif">{{$status_array[$digimart->status]}}</td>
                                <td class="border px-4 py-2 text-right">{{$digimart->updated_at}}</td>
                                <td class=" border px-4 py-2 text-center">
                                    <a href="{{ route('digimart.edit',['id'=>$digimart->id]) }}" class="block rounded bg-blue-600 p-2 text-white">編集</a>
                                    <a href="{{ route('digimart.delete',['id'=>$digimart->id]) }}" class="block rounded bg-red-600 p-2 mt-2 text-white">削除</a>
                                </td>
                            </tr>
                            @php $cnt++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                    <a href="{{ route('digimart.create'); }}" class="inline-flex items-center justify-center w-10 h-10 mr-2 text-white transition-colors duration-150 bg-gray-700 rounded-lg focus:shadow-outline hover:bg-gray-800 mt-5">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                            <path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" fill-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>