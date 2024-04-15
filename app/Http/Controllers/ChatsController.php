<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function fetchMessages()
    {
        $messages = Message::with('sender', 'receiver')
            ->where('sender_id', Auth::id())
            ->orWhere('receiver_id', Auth::id())
            ->get();

        return $this->success($messages, 'Messages fetched successfully');
    }

    public function sendMessage(Request $request)
    {
        $user = Auth::user();

        $message = new Message();
        $message->sender_id = $user->user_id;
        $message->receiver_id = $request->receiver_id;
        $message->message = $request->message;
        $message->save();

        // Pass the entire message object to the NewChatMessage constructor
        event(new NewChatMessage($message));

        return ['status' => 'Message Sent!'];
    }
}
