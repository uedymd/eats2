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

                        @isset($amount)
                        現在の設定値：{{ $amount }}円/ドル
                        @else
                        現在の設定値：自動設定
                        @endisset
                        <div class="mt-4 flex">
                            <div class="w-3/12">
                                {{Form::number('yen', null, ['step'=>0.001,'class'=>'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline require'])}}
                                @if (session('status'))
                                <div class="flex items-center bg-blue-500 text-white text-sm font-bold px-4 py-3" role="alert">
                                    <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z" />
                                    </svg>
                                    <p>{{ session('status') }}</p>
                                </div>
                                @endif
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