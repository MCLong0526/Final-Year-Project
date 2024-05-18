<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Notification;
use App\Models\User;
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

        // if comment content has @username, create notification for mentioned user, if has pare
        $pattern = '/@([a-zA-Z0-9_]+)/';
        preg_match_all($pattern, $comment->content, $matches);

        if (! empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $mentionedUser = User::where('username', $match)->first();
                if ($mentionedUser) {
                    $notification = new Notification();
                    $notification->receiver_id = $mentionedUser->user_id;
                    $notification->sender_id = auth()->user()->user_id;
                    $notification->type = 'post';
                    $notification->related_id = $comment->post_id;
                    $notification->status = 'Unread';
                    $notification->created_at = now();

                    if ($comment->parent_id === null && $comment->user_id !== $mentionedUser->user_id && $mentionedUser->user_id !== $comment->post->user_id) {
                        $notification->information = auth()->user()->username.' mentioned you in a comment in '.$comment->post->user->username.' post';

                    } elseif ($comment->parent_id !== null && $comment->user_id !== $mentionedUser->user_id && $mentionedUser->user_id !== $comment->post->user_id) {
                        if ($comment->replier_id === $mentionedUser->user_id) {
                            $notification->information = auth()->user()->username.' mentioned you in a comment when replying to you in '.$comment->post->user->username.' post';
                        } else {
                            $notification->information = auth()->user()->username.' mentioned you in a comment when replying to '.$comment->replier->username.' in '.$comment->post->user->username.' post';
                        }

                    } elseif ($comment->parent_id === null && $comment->user_id !== $mentionedUser->user_id && $mentionedUser->user_id === $comment->post->user_id) {
                        $notification->information = auth()->user()->username.' mentioned you in a comment in your post';

                    } elseif ($comment->parent_id !== null && $comment->user_id !== $mentionedUser->user_id && $mentionedUser->user_id === $comment->post->user_id) {
                        if ($comment->replier_id === $mentionedUser->user_id) {
                            $notification->information = auth()->user()->username.' mentioned you in a comment when replying to you in your post';
                        } else {
                            $notification->information = auth()->user()->username.' mentioned you in a comment when replying to '.$comment->replier->username.' in your post';
                        }
                    }

                    $notification->save();
                }
            }

            return response()->json(['message' => 'Comment created successfully', 'comment' => $comment]);

        }
        // Check if the comment is a reply and the replier is not the post owner, and it has no mentioned user
        if ($comment->parent_id !== null && empty($matches[1])) {
            //create notification for reply
            $notification = new Notification();
            $notification->receiver_id = $comment->replier_id;
            $notification->sender_id = auth()->user()->user_id;
            $notification->type = 'post';
            $notification->related_id = $comment->post_id;
            $notification->status = 'Unread';
            $notification->created_at = now();
            if ($comment->replier_id !== $comment->post->user_id) {
                $notification->information = auth()->user()->username.' replied to your comment in '.$comment->post->user->username.' post';
            } else {
                $notification->information = auth()->user()->username.' replied to your comment in your post';
            }

            $notification->save();

            return response()->json(['message' => 'Comment created successfully', 'comment' => $comment]);
        } elseif ($comment->parent_id === null && $comment->user_id !== $comment->post->user_id && empty($matches[1])) {
            //create notification for comment when auth user is not the post owner, and the comment is not a reply
            $notification = new Notification();
            $notification->receiver_id = $comment->post->user_id;
            $notification->sender_id = auth()->user()->user_id;
            $notification->information = auth()->user()->username.' commented on your post';
            $notification->type = 'post';
            $notification->related_id = $comment->post_id;
            $notification->status = 'Unread';
            $notification->created_at = now();

            $notification->save();

            return response()->json(['message' => 'Comment created successfully', 'comment' => $comment]);
        }

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
