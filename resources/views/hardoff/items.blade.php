<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            デジマート設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 overflow-hidden">
                    <div class="mt-10">
                        {{ $items->links() }}
                    </div>
                    <table class="table-auto break-all w-full mt-5 bg-white">
                        <caption class="bg-gray-100">
                            <div class="border px-4 py-2">{{$hardoff_data->title}}</div>
                        </caption>
                        <tbody>
                            @foreach($items as $item)
                            <tr>
                                <td class="border px-4 py-2">
                                    <p>
                                        <small>{{$item->jp_title}}</small>
                                    </p>
                                    <p class="mt-5">
                                        <strong>{{$item->en_title}}</strong>
                                    </p>
                                    <div class="slides mt-10">
                                        @php
                                        $images = unserialize($item->images);
                                        if(!empty($images)):
                                        @endphp
                                        <div><a href="{{$item->url}}" target="_blank"><img src="{{$images[0]}}" class="block"></a></div>
                                        @php
                                        endif;
                                        @endphp
                                    </div>
                                    <p class="mt-10">
                                        {!!number_format((float)$item->price)!!}円
                                    </p>
                                    <p class="mt-5">
                                        {!!number_format($item->doller)!!}ドル
                                    </p>
                                    <p class="mt-10">
                                        {{$item->updated_at}}更新
                                    </p>
                                </td>
                                <td class="border px-4 py-2">{!!nl2br($item->jp_content)!!}</td>
                                <td class="border px-4 py-2">{!!nl2br($item->en_content)!!}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-10">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>