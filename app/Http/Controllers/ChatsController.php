<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function fetchMessages()
    {
        $messagePerPage = request()->input('per_page', 10);
        $search = request('search_user');
        $user_id = null;
        if ($search == null) {
            $search = '';
        } else {
            $user_id = User::where('username', 'like', '%'.$search.'%')->first()->user_id;
        }

        // Get the latest messages for each chat (combination of sender_id and receiver_id)
        $messages = Message::with('sender', 'receiver', 'pictures')

            ->where('sender_id', Auth::id())
            ->orWhere('receiver_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) {
                return $message->sender_id == Auth::id() ? $message->receiver_id : $message->sender_id;
            })
            ->flatMap(function ($messages) use ($messagePerPage) {
                return $messages->take($messagePerPage)->sortBy('created_at');
            })
            //filter the messages based on the search, if the search is not empty, then filter the messages based on $user_id
            ->when($search != '', function ($query) use ($user_id) {
                return $query->filter(function ($message) use ($user_id) {
                    return $message->sender_id == $user_id || $message->receiver_id == $user_id;
                });
            });

        return $this->success(data: $messages, message: 'Messages retrieved successfully');
    }

    public function sendMessage(Request $request)
    {
        $user = Auth::user();

        $message = new Message();
        $message->sender_id = $user->user_id;
        $message->receiver_id = $request->receiver_id;
        $message->message = $request->message;
        $message->save();

        $this->saveMessagePictures($request, $message->id);

        // Pass the entire message object to the NewChatMessage constructor
        event(new NewChatMessage($message));

        return ['status' => 'Message Sent!'];
    }

    public function saveMessagePictures(Request $request, string $id)
    {
        $message = Message::findOrFail($id);
        // Get the images from the request
        $images = $request->images;

        // Use map to save each image
        collect($images)->each(function ($image) use ($message) {
            // Get the image format (png, jpg, jpeg)
            $imageFormat = explode('/', mime_content_type($image['url']))[1];
            // Decode the base64-encoded image URL
            $decodedImage = base64_decode(str_replace('data:image/'.$imageFormat.';base64,', '', $image['url']));

            // Generate a unique file name with the correct extension
            $fileName = uniqid().'.'.$imageFormat;

            // Store the image in the storage/app/public/images/items directory
            Storage::disk('public')->put('images/messages/'.$fileName, $decodedImage);

            // Create a new item picture record in the database
            $message->pictures()->create([
                'picture_path' => 'images/messages/'.$fileName,
                'message_id' => $message->id,
            ]);
        });

        return ['status' => 'Pictures saved!'];

    }

    public function markMessagesAsRead(Request $request)
    {
        $sender_id = $request->sender_id;
        $receiver_id = $request->receiver_id;

        Message::where('sender_id', $sender_id)
            ->where('receiver_id', $receiver_id)
            ->orWhere(function ($query) use ($sender_id, $receiver_id) {
                $query->where('sender_id', $receiver_id)
                    ->where('receiver_id', $sender_id);
            })
            ->update(['status' => 'read']);

        return response()->json(['status' => 'success'], 200);
    }
}
