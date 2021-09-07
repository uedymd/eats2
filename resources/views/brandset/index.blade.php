<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ブランドセット設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between">
                        <a href="{{ route('setting.brandset.create'); }}" class="inline-flex items-center justify-center w-10 h-10 mr-2 text-indigo-100 transition-colors duration-150 bg-indigo-700 rounded-lg focus:shadow-outline hover:bg-indigo-800 mt-5">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" fill-rule="evenodd"></path>
                            </svg>
                        </a>
                    </div>
                    <table class="table-auto w-full mt-5">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">設定名</th>
                                <th class="border px-4 py-2">ブランド</th>
                                <th class="border px-4 py-2 w-1/12"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($brand_sets as $brand_set)
                            <tr>
                                <td class="border px-4 py-2">{{$brand_set->name}}</td>
                                <td class="border px-4 py-2">{!!nl2br($brand_set->set)!!}</td>
                                <td class=" border px-4 py-2 text-center">
                                    <a href="{{ route('setting.brandset.edit',['id'=>$brand_set->id]) }}" class="block rounded bg-blue-600 p-2 text-white">編集</a>
                                    <a href="{{ route('setting.brandset.destroy',['id'=>$brand_set->id]) }}" class="block rounded bg-red-600 p-2 mt-2 text-white">削除</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <a href="{{ route('setting.brandset.create'); }}" class="inline-flex items-center justify-center w-10 h-10 mr-2 text-indigo-100 transition-colors duration-150 bg-indigo-700 rounded-lg focus:shadow-outline hover:bg-indigo-800 mt-5">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                            <path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" fill-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>