<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageReplyRequest;
use App\Http\Requests\UpdateMessageReplyRequest;
use App\Models\MessageReply;

class MessageReplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMessageReplyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMessageReplyRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MessageReply  $messageReply
     * @return \Illuminate\Http\Response
     */
    public function show(MessageReply $messageReply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MessageReply  $messageReply
     * @return \Illuminate\Http\Response
     */
    public function edit(MessageReply $messageReply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMessageReplyRequest  $request
     * @param  \App\Models\MessageReply  $messageReply
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMessageReplyRequest $request, MessageReply $messageReply)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MessageReply  $messageReply
     * @return \Illuminate\Http\Response
     */
    public function destroy(MessageReply $messageReply)
    {
        //
    }
}
