<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ebay設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="table-auto w-full mt-5">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2 w-1/12">EATS ID</th>
                                <th class="border px-4 py-2 w-1/12">アイテム</th>
                                <th class="border px-4 py-2 w-3/12">タイトル</th>
                                <th class="border px-4 py-2 w-1/12">仕入元</th>
                                <th class="border px-4 py-2 w-1/12">販売価格</th>
                                <th class="border px-4 py-2">エラー</th>
                                <th class="border px-4 py-2">ステータス</th>
                                <th class="border px-4 py-2 w-2/12">追跡</th>
                                <th class="border px-4 py-2 w-1/12">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border px-4 py-2">
                                    {{$ebay->id}}
                                </td>
                                <td class="border px-4 py-2">
                                    @if($ebay->image!=='')
                                    <img src="{{$ebay->image}}" alt="" class="block" style="max-width:100%;height:auto;">
                                    @endif
                                    @if(!is_null($ebay->view_url))
                                    <a href="{{$ebay->view_url}}" target="_blank" class="block rounded bg-gray-500 p-2 text-white text-center mt-2">View</a>
                                    @else
                                    <small>詳細取得中</small>
                                    @endif
                                </td>
                                <td class="border px-4 py-2">
                                    {{$ebay->title}}
                                    @if($ebay->ebay_id>0)
                                    <br>【{{$ebay->ebay_id}}】
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    @if(isset($suppliers[$ebay->id]))
                                    {{$ebay->site}}
                                    <a href="{{$suppliers[$ebay->id]}}" target="_blank" class="block rounded bg-gray-500 p-2 text-white text-center mt-2">View</a>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 text-right">${{number_format($ebay->price)}}
                                </td>
                                <td class="border px-4 py-2">
                                    <?php
                                    $errors = unserialize($ebay->error);
                                    if ($errors !== false) {
                                        foreach ($errors as $error) {
                                            echo $error;
                                            if ($error !== end($errors)) {
                                                echo "<br>";
                                            }
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="border px-4 py-2 text-center">{{$ebay->status_code}}</td>
                                <td class="border px-4 py-2 text-right">{{$ebay->tracking_at}}</td>
                                <td class=" border px-4 py-2 text-center">
                                    <a href="{{ route('ebay.delete',['id'=>$ebay->id]) }}" class="block rounded bg-red-600 p-2 mt-2 text-white">出品取消</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 overflow-hidden">
                    <table class="table-auto break-all w-full mt-5 bg-white">
                        <tbody>
                            <tr>
                                <td class="border px-4 py-2">
                                    <p>
                                        {{$target->id}}
                                    </p>
                                    <p>
                                        {{$target->jp_title}}
                                    </p>
                                    <p class="mt-5">
                                        {{$target->en_title}}
                                    </p>
                                    <script>
                                        $(function() {
                                            $('.slides').slick({
                                                slidesToShow: 1,
                                                slidesToScroll: 1,
                                                arrows: true,
                                                fade: false,
                                                asNavFor: '.thumbs'
                                            });
                                            $('.thumbs').slick({
                                                slidesToShow: 3,
                                                slidesToScroll: 1,
                                                asNavFor: '.slides',
                                                dots: false,
                                                centerMode: true,
                                                focusOnSelect: true
                                            });
                                        })
                                    </script>
                                    <div class="slides mt-10">
                                        @php
                                        $images = unserialize($target->images);
                                        @endphp
                                        @foreach((array)$images as $image)
                                        <div><img src="{{$image}}" class="block"></div>
                                        @endforeach
                                    </div>
                                    <div class="thumbs mt-5">
                                        @php
                                        $images = unserialize($target->images);
                                        @endphp
                                        @foreach((array)$images as $image)
                                        <div><img src="{{$image}}" class="block"></div>
                                        @endforeach
                                    </div>
                                    <p class="mt-10">
                                        {!!number_format((float)$target->price)!!}円
                                    </p>
                                    <p class="mt-5">
                                        {!!number_format($target->doller)!!}ドル
                                    </p>
                                    <p class="mt-10">
                                        {{$target->updated_at}}更新
                                    </p>
                                </td>
                                <td class="border px-4 py-2">{!!nl2br($target->jp_content)!!}</td>
                                <td class="border px-4 py-2">{!!nl2br($target->en_content)!!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>