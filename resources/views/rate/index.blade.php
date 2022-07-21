<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            金額レート設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between">
                        <a href="{{ route('setting.rateset.create'); }}" class="inline-flex items-center justify-center w-10 h-10 mr-2 text-indigo-100 transition-colors duration-150 bg-indigo-700 rounded-lg focus:shadow-outline hover:bg-indigo-800 mt-5">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" fill-rule="evenodd"></path>
                            </svg>
                        </a>
                    </div>
                    <table class="table-auto w-full mt-5">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">設定名</th>
                                <th class="border px-4 py-2">設定内容</th>
                                <th class="border px-4 py-2 w-1/12"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rate_sets as $rate_set)
                            <tr>
                                <td class="border px-4 py-2">{{$rate_set->name}}</td>
                                <td class="border px-4 py-2">
                                    @php
                                    $settings = unserialize($rate_set->set);
                                    @endphp
                                    @foreach($settings as $setting)

                                    @if(!empty($setting['min']))
                                        {{number_format($setting['min'])}}円以上　
                                    @endif
                                    @if(!empty($setting['max']))
                                        {{number_format($setting['max'])}}円未満　
                                    @endif
                                    @if(!empty($setting['rate']))
                                        =>　<span class="text-blue-700"><strong>{{number_format($setting['rate'])}}円</strong></span><br>
                                    @endif

                                    @endforeach
                                </td>
                                <td class=" border px-4 py-2 text-center">
                                    <a href="{{ route('setting.rateset.edit',['id'=>$rate_set->id]) }}" class="block rounded bg-blue-600 p-2 text-white">編集</a>
                                    <a href="{{ route('setting.rateset.destroy',['id'=>$rate_set->id]) }}" class="block rounded bg-red-600 p-2 mt-2 text-white">削除</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <a href="{{ route('setting.rateset.create'); }}" class="inline-flex items-center justify-center w-10 h-10 mr-2 text-indigo-100 transition-colors duration-150 bg-indigo-700 rounded-lg focus:shadow-outline hover:bg-indigo-800 mt-5">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                            <path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" fill-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>