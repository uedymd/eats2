<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            楽天設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="font-semibold text-xm text-gray-800 leading-tight">
                        楽天検索設定「{{$rakuten->title}}」を削除します。
                    </h3>
                    <div class="mt-5 text-gray-600 dark:text-gray-400 text-sm">
                        設定を削除する場合は、以下のボタンをクリックしてください。
                    </div>
                    <div class="mt-5 text-gray-600 dark:text-gray-400 text-sm w-2/12">
                        <a href="{{ route('rakuten.destroy',['id'=>$rakuten->id]) }}" class="block rounded text-center bg-red-300 p-3">削除</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>