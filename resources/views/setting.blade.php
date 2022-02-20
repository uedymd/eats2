<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            サイトごとの共通設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('setting.update',['site'=>$site]) }}">
                        @csrf

                        <?php
                        if (!isset($settings[0])) {
                            $ng_title = "";
                            $ng_content = "";
                        } else {
                            $ng_title = $settings[0]->ng_title;
                            $ng_content = $settings[0]->ng_content;
                        }
                        ?>

                        <div class="mt-4">
                            @if (session('error'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-10 rounded relative" role="alert">
                                <strong class="font-bold">エラー</strong>
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                            @endif
                            <div class="flex">
                                <div class="flex-grow ml-5">
                                    {{Form::label('ng_titles', 'タイトル除外キーワード', ['class' => 'awesome red','id' => 'asd'])}}
                                    {{Form::text('ng_titles_word', "", ['class' => 'form-control w-10/12', 'id' => 'ng_title'])}}
                                    {{Form::textarea('ng_titles', $ng_title, ['class' => 'form-control w-10/12 mt-5', 'id' => 'ng_title', 'rows' => '10','readonly' => true])}}
                                </div>
                                <div class="flex-grow ml-5">
                                    {{Form::label('ng_contents', 'コンテンツ除外キーワード', ['class' => 'awesome red','id' => 'asd'])}}
                                    {{Form::text('ng_contents_word', "", ['class' => 'form-control w-10/12', 'id' => 'ng_title'])}}
                                    {{Form::textarea('ng_contentss', $ng_content, ['class' => 'form-control w-10/12 mt-5', 'id' => 'ng_content', 'rows' => '10','readonly' => true])}}
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