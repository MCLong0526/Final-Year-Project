<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Notification;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Set the timezone to Malaysia
        date_default_timezone_set('Asia/Kuala_Lumpur');

        // Validation rules
        $rules = [
            'content' => 'required|string',
            'post_id' => 'required|exists:posts,post_id',
            'parent_id' => 'nullable|exists:comments,id',
            'replier_id' => 'nullable|exists:users,user_id',
        ];

        // Validate the request
        $validatedData = $request->validate($rules);

        // Create a new comment
        $comment = new Comment();
        $comment->content = $validatedData['content'];
        $comment->post_id = $validatedData['post_id'];
        $comment->parent_id = $validatedData['parent_id'];
        // Assuming you have a User model with a user_id foreign key
        $comment->user_id = auth()->user()->user_id; // Adjust this based on your User model
        $comment->replier_id = $validatedData['replier_id'];
        $comment->created_at = now();

        $comment->save();

        // Check if the comment is a reply and the replier is not the post owner
        if ($comment->parent_id !== null && $comment->replier_id !== $comment->post->user_id) {
            //create notification for reply
            $notification = new Notification();
            $notification->receiver_id = $comment->replier_id;
            $notification->sender_id = auth()->user()->user_id;
            $notification->information = auth()->user()->username.' replied to your comment';
            $notification->status = 'Unread';
            $notification->created_at = now();

            $notification->save();
        } elseif ($comment->parent_id === null && $comment->user_id !== $comment->post->user_id) {
            //create notification for comment
            $notification = new Notification();
            $notification->receiver_id = $comment->post->user_id;
            $notification->sender_id = auth()->user()->user_id;
            $notification->information = auth()->user()->username.' commented on your post';
            $notification->status = 'Unread';
            $notification->created_at = now();

            $notification->save();
        }

        return response()->json(['message' => 'Comment created successfully', 'comment' => $comment]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    //get all comments of selected post
    public function getCommentsByPostId($postId)
    {
        $comments = Comment::where('post_id', $postId)->with('user', 'replier')->get();

        return $this->success($comments, 'Comments retrieved successfully', 200);
    }
}
