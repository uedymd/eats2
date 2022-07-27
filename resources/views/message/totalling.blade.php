<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            メッセージ集計
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    {{$startDate}}〜{{$endDate}}
                    <table class="table-auto w-6/12 mt-5">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2 w-1/12">ユーザー</th>
                                <th class="border px-4 py-2 w-1/12">カウント</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($replies as $reply)
                                <tr>
                                    <td class="border px-4 py-2 w-1/12">{{$reply->name}}</td>
                                    <td class="border px-4 py-2 w-1/12 text-right">{{$reply->count_userId}}回</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="flex justify-between w-6/12 mt-5">
                        @php
                            $prev_year = date('Y',strtotime($startDate.'-1 month'));
                            $prev_month = date('m',strtotime($startDate.'-1 month'));
                            $next_year = date('Y',strtotime($startDate.'+1 month'));
                            $next_month = date('m',strtotime($startDate.'+1 month'));
                            
                        @endphp
                        <div><a href="{{ route('message.totalling',['year'=>$prev_year,'month'=>$prev_month]) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">前の月</a></div>
                        <div><a href="{{ route('message.totalling',['year'=>$next_year,'month'=>$next_month]) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">前の月</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>