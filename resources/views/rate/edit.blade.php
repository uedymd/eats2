<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ドル円レート設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('setting.rate.update') }}">
                        @csrf

                        <div class="mt-4 flex">
                            <div class="w-3/12">
                                {{Form::number('yen', $rate->amount, ['step'=>0.001,'class'=>'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline require'])}}
                                <div class="mt-4">
                                    <p>空白に設定すると自動設定になります</p>
                                </div>
                            </div>
                            <div class="p-2">
                                円/ドル
                            </div>
                            <div class="w-2/12">
                                <x-button class="ml-4">
                                    {{ __('登録') }}
                                </x-button>
                            </div>
                        </div>




                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>