<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use App\Events\NewChatMessage;

class ChatController extends Controller
{
    public function rooms(Request $request) {
        return ChatRoom::all();
    }

    public function messages(Request $request, $rId) {
        return ChatMessage::where('chat_room_id', $rId)
            ->with('user')
            ->orderBy('created_at','DESC')
            ->get();
    }

    public function newMessage(Request $request, $rId) {
        $newMessage = new ChatMessage;
        $newMessage->user_id = Auth::id();
        $newMessage->chat_room_id = $rId;
        $newMessage->message = $request->message;
        $newMessage->save();

        broadcast(new NewChatMessage($newMessage))->toOthers();

        return $newMessage;
    }

}
