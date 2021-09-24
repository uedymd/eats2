<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            価格レート設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('setting.template.store') }}">
                        @csrf

                        <div class="mt-4">
                            <div>
                                <x-label for="title" :value="__('設定名')" />
                                
                                <x-input id="title" class="block mt-1 w-8/12" type="text" name="title" :value="old('title')" required autofocus />
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <x-label for="source" :value="__('HTMLソース')" />
                            {{Form::textarea('source', null, ['class' => 'form-control block w-full', 'id' => 'source', 'placeholder' => 'テンプレートのHTMLソースを入力', 'rows' => '10'])}}
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('登録') }}
                            </x-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>