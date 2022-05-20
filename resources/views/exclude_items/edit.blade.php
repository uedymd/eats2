<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            削除対象キーワード設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('setting.excludeitems.update') }}">
                        @csrf

                        <div class="mt-4">
                            <div>
                                <x-label for="keywords" :value="__('削除対象キーワード')" />

                                {{Form::textarea('keywords', "$items->keywords", ['class' => 'form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50', 'id' => 'set', 'placeholder' => '削除対象キーワード   を一行づつ入力してください。', 'rows' => '15'])}}
                            </div>
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