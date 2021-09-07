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

                    <form method="POST" action="{{ route('setting.rateset.store') }}">
                        @csrf

                        <div class="mt-4">
                            <div>
                                <x-label for="name" :value="__('設定名')" />

                                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            </div>
                        </div>

                        <div class="mt-4 flex">
                            <div class="w-3/12">
                                最小値（円以上）
                            </div>
                            <div class="w-3/12 ml-5">
                                最大値（円未満）
                            </div>
                            <div class="w-3/12 ml-5">
                                レート（円上乗せ）
                            </div>
                        </div>

                        <div class="mt-4 flex input_set">
                            <div class="w-3/12">
                                {{Form::number('price_min[]', null, ['placeholder' => '円以上','class'=>'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline require'])}}
                            </div>
                            <div class="w-3/12 ml-5">
                                {{Form::number('price_max[]', null, ['placeholder' => '円未満','class'=>'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline require'])}}
                            </div>
                            <div class="w-3/12 ml-5">
                                {{Form::number('price_rate[]', null, ['placeholder' => '円上乗せ','class'=>'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline require'])}}
                            </div>
                            <div class="w-3/12 ml-5">
                                <span class="rate_add inline-flex items-center justify-center w-10 h-10 mr-2 text-white transition-colors duration-150 bg-gray-700 rounded-lg focus:shadow-outline hover:bg-gray-800 ">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </span>
                            </div>
                        </div>




                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('登録') }}
                            </x-button>
                        </div>
                    </form>

                    <div class="rate_set_model hidden mt-4 input_set">
                        <div class="w-3/12">
                            {{Form::number('price_min[]', null, ['placeholder' => '円以上','class'=>'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline'])}}
                        </div>
                        <div class="w-3/12 ml-5">
                            {{Form::number('price_max[]', null, ['placeholder' => '円未満','class'=>'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline'])}}
                        </div>
                        <div class="w-3/12 ml-5">
                            {{Form::number('price_rate[]', null, ['placeholder' => '円上乗せ','class'=>'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline'])}}
                        </div>
                        <div class="w-3/12 ml-5">
                            <span class="rate_add inline-flex items-center justify-center w-10 h-10 mr-2 text-white transition-colors duration-150 bg-gray-700 rounded-lg focus:shadow-outline hover:bg-gray-800 ">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                            <span class="rate_remove inline-flex items-center justify-center w-10 h-10 mr-2 text-white transition-colors duration-150 bg-gray-700 rounded-lg focus:shadow-outline hover:bg-gray-800 ">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>