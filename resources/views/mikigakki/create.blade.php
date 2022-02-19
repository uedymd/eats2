<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            三木楽器設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('mikigakki.store') }}">
                        @csrf

                        <div class="mt-4">
                            <div>
                                <x-label for="title" :value="__('設定名')" />

                                <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            </div>
                        </div>

                        <div class="mt-4 flex">
                            <div class="flex-1 flex-shrink-0 w-9/12">
                                <x-label for="keyword" :value="__('検索URL')" />

                                <x-input id="url" class="block mt-1 w-full" type="text" name="url" :value="old('url')" required autofocus />
                            </div>
                            <div class="flex-shrink-0 w-3/12 ml-5">
                                <x-label for="keyword" :value="__('対象ブランド')" />
                                {{ Form::select(
                                    'brand_set_id', 
                                    $selector,
                                    null,
                                    ['class'=>'block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500','required']
                                ) }}
                            </div>
                        </div>

                        <div class="mt-4 flex">
                            <div class="flex-1">
                                <x-label for="ng_keyword" :value="__('除外キーワード')" />

                                <x-input id="ng_keyword" class="block mt-1 w-10/12" type="text" name="ng_keyword" :value="old('ng_keyword')" autofocus />
                            </div>
                            <div class="flex-1">
                                <x-label for="ng_url" :value="__('除外URL')" />

                                <x-input id="ng_url" class="block mt-1 w-10/12" type="text" name="ng_url" :value="old('ng_url')" autofocus />
                            </div>
                            <div class="flex-1">
                                <x-label for="rate" :value="__('レート')" />

                                {{ Form::select(
                                    'rate_set_id', 
                                    $rate_selector,
                                    null,
                                    ['class'=>'block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500','required']
                                ) }}
                            </div>
                        </div>



                        <div class="mt-4 flex">
                            <div class="flex-1">
                                <x-label for="price_min" :value="__('ebayカテゴリー')" />

                                <x-input id="ebay_category" class="mt-1 w-10/12" type="text" name="ebay_category" :value="old('ebay_category')" autofocus required />
                            </div>
                            <div class="flex-1">
                                <x-label for="best_offer" :value="__('Best Offer')" />
                                <div class="mt-2">
                                    {{ Form::radio('best_offer', '1',null,['id'=>'best_offer_true'] );}}
                                    {{Form::label('best_offer_true','有効',['class'=>'custom-control-label mr-10'])}}
                                    {{ Form::radio('best_offer', '0',null,['id'=>'best_offer_false'] );}}
                                    {{Form::label('best_offer_false','無効',['class'=>'custom-control-labelx'])}}
                                </div>
                            </div>
                            <div class="flex-1">
                                <x-label for="condition" :value="__('コンディション')" />
                                <div class="mt-2">
                                    {{ Form::radio('condition', '1',null,['id'=>'condition_new'] );}}
                                    {{Form::label('condition_new','新品',['class'=>'custom-control-label mr-10','required'])}}
                                    {{ Form::radio('condition', '2',null,['id'=>'condition_used'] );}}
                                    {{Form::label('condition_used','中古',['class'=>'custom-control-labelx','required'])}}
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex">
                            <div class="flex-1">
                                <x-label for="sku" :value="__('SKU')" />
                                <x-input id="sku" class="block mt-1" type="text" name="sku" :value="old('sku')" autofocus />
                            </div>
                            <div class="flex-1">
                                <x-label for="type" :value="__('Type')" />
                                <x-input id="type" class="block mt-1" type="text" name="type" :value="old('type')" autofocus required />
                            </div>
                            <div class="flex-1">
                                <x-label for="keyword" :value="__('テンプレート')" />
                                {{ Form::select(
                                    'template', 
                                    $template_selector,
                                    null,
                                    ['class'=>'block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500','required']
                                    ) }}
                            </div>
                        </div>
                        <div class="mt-4 flex">
                            <div class="flex-1">
                                <x-label for="payment_profile" :value="__('Payment Profile')" />
                                <x-input id="payment_profile" class="block mt-1 w-10/12 mr-3" type="text" name="payment_profile" :value="old('payment_profile')" autofocus required />
                            </div>
                            <div class="flex-1">
                                <x-label for="return_profile" :value="__('Return Profile')" />
                                <x-input id="return_profile" class="block mt-1 w-10/12 mr-3" type="text" name="return_profile" :value="old('return_profile')" autofocus required />
                            </div>
                            <div class="flex-1">
                                <x-label for="shipping_profile" :value="__('Shipping Profile')" />
                                <x-input id="shipping_profile" class="block mt-1 w-10/12 mr-3" type="text" name="shipping_profile" :value="old('shipping_profile')" autofocus required />
                            </div>
                            <div class="flex-1">
                                <x-label for="priority" :value="__('優先順位')" />
                                <x-input id="priority" class="block mt-1 w-10/12 mr-3" type="number" name="priority" :value="old('priority',2)" autofocus required />
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