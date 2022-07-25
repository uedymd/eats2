<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            メッセージツール
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mt-10">
                        {{ $messages->links() }}
                    </div>
                    <table class="table-auto w-full mt-5">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2 w-1/12">From</th>
                                <th class="border px-4 py-2 w-6/12">Subject</th>
                                <th class="border px-4 py-2 w-1/12">Item Ends</th>
                                <th class="border px-4 py-2 w-1/12">Received</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $message)
                            <tr>
                                <td class="border px-4 py-2">
                                    {{$message->Sender}}
                                </td>
                                <td class="border px-4 py-2">
                                    <p class="text-blue-600">
                                        <a href="{{ route('message.show',['id'=>$message->id]) }}">{{$message->Subject}}</a>
                                    </p>
                                </td>
                                <td class=" border px-4 py-2 text-sm">
                                    {{$message->ItemEndTime}}
                                </td>
                                <td class="border px-4 py-2 text-sm">
                                    {{$message->ReceiveDate}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-10">
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>