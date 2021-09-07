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
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('rakuten.update',['id'=>$rakuten->id]) }}">
                        @csrf

                        <div class="mt-4">
                            <div class="flex">
                                <div>
                                    <x-label for="title" :value="__('設定名')" />
                                    <x-input id="title" class="block mt-1 w-full" type="text" name="title" value="{{$rakuten->title}}" required autofocus />
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="flex">
                                <div class="flex-grow">
                                    <x-label for="keyword" :value="__('検索キーワード')" />

                                    <x-input id="keyword" class="block mt-1 w-full" type="text" name="keyword" value="{{$rakuten->keyword}}" required autofocus />
                                </div>
                                <div class="flex-grow ml-5">
                                    <x-label for="ng_keyword" :value="__('除外キーワード')" />

                                    <x-input id="ng_keyword" class="block mt-1 w-full" type="text" name="ng_keyword" value="{{$rakuten->ng_keyword}}" autofocus />
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex">
                            <div class="flex-shrink-0 w-8/12">
                                <x-label for="genre" :value="__('ジャンル')" />

                                <x-input id="genre" class="block mt-1 w-full" type="text" name="genre" value="{{$rakuten->genre}}" autofocus />
                                <x-input id="genre_id" type="hidden" name="genre_id" value="{{$rakuten->genre_id}}" />
                            </div>

                            <div class="flex-shrink-0 w-3/12 ml-5">
                                <x-label for="keyword" :value="__('対象ブランド')" />
                                {{ Form::select(
                                    'brand_Set', 
                                    $selector,
                                    null,
                                    ['class'=>'block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500','required']
                                ) }}
                            </div>


                        </div>

                        <div class="mt-4">
                            <div class="block rakuten__genre--button">
                                <ul class="rakuten__genre--selector">
                                    <li class="rakuten__genre--parent hasChild" data-genre="0"><span class="block p-3 mt-5 rounded bg-blue-200 text-center w-2/12">ジャンルを選択</span></li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-4 flex">
                            <div class="flex-1">
                                <x-label for="ng_keyword" :value="__('除外キーワード')" />

                                <x-input id="ng_keyword" class="block mt-1 w-10/12" type="text" name="ng_keyword" value="{{$rakuten->ng_url}}" autofocus />
                            </div>
                            <div class="flex-1">
                                <x-label for="ng_url" :value="__('除外URL')" />

                                <x-input id="ng_url" class="block mt-1 w-10/12" type="text" name="ng_url" value="{{$rakuten->ng_url}}" autofocus />
                            </div>
                        </div>


                        <div class="mt-4 flex">
                            <div class="flex-1">
                                <x-label for="price_min" :value="__('最小価格')" />

                                <x-input id="price_min" class="mt-1 w-10/12" type="number" name="price_min" value="{{$rakuten->price_min}}" autofocus />円
                            </div>
                            <div class="flex-1">
                                <x-label for="price_max" :value="__('最大価格')" />

                                <x-input id="price_max" class="mt-1 w-10/12" type="number" name="price_max" value="{{$rakuten->price_max}}" autofocus />円
                            </div>
                            <div class="flex-1">
                                <x-label for="rate" :value="__('レート')" />

                                <x-input id="rate" class="mt-1 w-10/12" type="text" name="rate" value="{{$rakuten->rate}}" autofocus />倍
                            </div>
                        </div>

                        <div class="mt-4">
                            <div>
                                <label for="status">ステータス</label>
                                {{ Form::select('status',$status_array,$rakuten->status) }}

                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('更新') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>