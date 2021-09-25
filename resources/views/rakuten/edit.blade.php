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
                            <div>
                                <x-label for="title" :value="__('設定名')" />

                                <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title',$rakuten->title)" required autofocus />
                            </div>
                        </div>

                        <div class="mt-4 flex">
                            <div class="flex-shrink-0 w-8/12">
                                <x-label for="keyword" :value="__('検索キーワード')" />

                                <x-input id="keyword" class="block mt-1 w-full" type="text" name="keyword" :value="old('keyword',$rakuten->keyword)" required autofocus />
                            </div>
                            <div class="flex-shrink-0 w-3/12 ml-5">
                                <x-label for="keyword" :value="__('対象ブランド')" />
                                {{ Form::select(
                                    'brand_set_id', 
                                    $selector,
                                    $rakuten->brand_set_id,
                                    ['class'=>'block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500','required']
                                ) }}
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="flex-shrink-0 w-8/12">
                                <x-label for="genre" :value="__('楽天ジャンル')" />

                                <x-input id="genre" class="block mt-1 w-full" type="text" name="genre" :value="old('genre',$rakuten->genre)" autofocus />
                                <x-input id="genre_id" type="hidden" name="genre_id" :value="old('genre_id',$rakuten->genre_id)" />
                            </div>
                            <div class="block rakuten__genre--button">
                                <ul class="rakuten__genre--selector">
                                    <li class="rakuten__genre--parent hasChild" data-genre="0"><span class="block p-3 mt-5 rounded bg-blue-200 text-center w-2/12">ジャンルを選択</span></li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-4 flex">
                            <div class="flex-1">
                                <x-label for="ng_keyword" :value="__('除外キーワード')" />

                                <x-input id="ng_keyword" class="block mt-1 w-10/12" type="text" name="ng_keyword" :value="old('ng_keyword',$rakuten->ng_keyword)" autofocus />
                            </div>
                            <div class="flex-1">
                                <x-label for="ng_url" :value="__('除外URL')" />

                                <x-input id="ng_url" class="block mt-1 w-10/12" type="text" name="ng_url" :value="old('ng_url',$rakuten->ng_url)" autofocus />
                            </div>
                        </div>



                        <div class="mt-4 flex">
                            <div class="flex-1">
                                <x-label for="price_min" :value="__('最小価格')" />

                                <x-input id="price_min" class="mt-1 w-10/12" type="number" name="price_min" :value="old('price_min',$rakuten->price_min)" autofocus />円
                            </div>
                            <div class="flex-1">
                                <x-label for="price_max" :value="__('最大価格')" />

                                <x-input id="price_max" class="mt-1 w-10/12" type="number" name="price_max" :value="old('price_max',$rakuten->price_max)" autofocus />円
                            </div>
                            <div class="flex-1">
                                <x-label for="rate" :value="__('レート')" />

                                {{ Form::select(
                                    'rate_set_id', 
                                    $rate_selector,
                                    $rakuten->rate_set_id,
                                    ['class'=>'block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500','required']
                                ) }}
                            </div>
                        </div>
                        
                        <div class="mt-4 flex">
                            <div class="flex-1">
                                <x-label for="price_min" :value="__('ebayカテゴリー')" />

                                <x-input id="ebay_category" class="mt-1 w-10/12" type="text" name="ebay_category" :value="old('ebay_category',$rakuten->ebay_category)" autofocus required />
                            </div>
                            <div class="flex-1">
                                <x-label for="best_offer" :value="__('Best Offer')" />
                                <div class="mt-2">
                                    @php
                                        $best_offer = [
                                            'best_offer_true' => false,
                                            'best_offer_false' => false,
                                        ];
                                        if($rakuten->best_offer==1){
                                            $best_offer = [
                                                'best_offer_true' => true,
                                                'best_offer_false' => false,
                                            ];
                                        }elseif(!is_null($rakuten->best_offer)&&$rakuten->best_offer==0){
                                            $best_offer = [
                                                'best_offer_true' => true,
                                                'best_offer_false' => false,
                                            ];
                                        }
                                    @endphp
                                    {{ Form::radio('best_offer', '1',$best_offer['best_offer_true'],['id'=>'best_offer_true'] );}}
                                    {{Form::label('best_offer_true','有効',['class'=>'custom-control-label mr-10'])}}
                                    {{ Form::radio('best_offer', '0',$best_offer['best_offer_false'],['id'=>'best_offer_false'] );}}
                                    {{Form::label('best_offer_false','無効',['class'=>'custom-control-labelx'])}}
                                </div>
                            </div>
                            <div class="flex-1">
                                <x-label for="condition" :value="__('コンディション')" />
                                <div class="mt-2">
                                    @php
                                        $condition = [
                                            'condition_new' => false,
                                            'condition_used' => false,
                                        ];
                                        if($rakuten->condition==1){
                                            $condition = [
                                                'condition_new' => true,
                                                'condition_used' => false,
                                            ];
                                        }elseif(!is_null($rakuten->condition)&&$rakuten->condition==2){
                                            $condition = [
                                                'condition_new' => false,
                                                'condition_used' => true,
                                            ];
                                        }
                                    @endphp
                                    {{ Form::radio('condition', '1',$condition['condition_new'],['id'=>'condition_new','required'] );}}
                                    {{Form::label('condition_new','新品',['class'=>'custom-control-label mr-10'])}}
                                    {{ Form::radio('condition', '2',$condition['condition_used'],['id'=>'condition_used','required'] );}}
                                    {{Form::label('condition_used','中古',['class'=>'custom-control-labelx'])}}
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex">
                            <div class="flex-1">
                                <x-label for="sku" :value="__('SKU')" />
                                <x-input id="sku" class="block mt-1" type="text" name="sku" :value="old('sku',$rakuten->sku)" autofocus />
                            </div>
                            <div class="flex-1">
                                <x-label for="type" :value="__('Type')" />
                                <x-input id="type" class="block mt-1" type="text" name="type" :value="old('type',$rakuten->type)" autofocus required />
                            </div>
                            <div class="flex-1">
                                <x-label for="keyword" :value="__('テンプレート')" />
                                {{ Form::select(
                                    'template', 
                                    $template_selector,
                                    old('template',$rakuten->template),
                                    ['class'=>'block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500','required']
                                ) }}
                            </div>

                        </div>
                        <div class="mt-4 flex">
                            <div class="flex-1">
                                <x-label for="payment_profile" :value="__('Payment Profile')" />
                                <x-input id="payment_profile" class="block mt-1 w-10/12 mr-3" type="text" name="payment_profile" :value="old('payment_profile',$rakuten->payment_profile)" autofocus required />
                            </div>
                            <div class="flex-1">
                                <x-label for="return_profile" :value="__('Return Profile')" />
                                <x-input id="return_profile" class="block mt-1 w-10/12 mr-3" type="text" name="return_profile" :value="old('return_profile',$rakuten->return_profile)" autofocus required />
                            </div>
                            <div class="flex-1">
                                <x-label for="shipping_profile" :value="__('Shipping Profile')" />
                                <x-input id="shipping_profile" class="block mt-1 w-10/12 mr-3" type="text" name="shipping_profile" :value="old('shipping_profile',$rakuten->shipping_profile)" autofocus required />
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
                                {{ __('登録') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>