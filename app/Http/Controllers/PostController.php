<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $postPerPage = request()->input('per_page', 10);
        $search = request('search');

        $posts = Post::with('user', 'comments', 'likes')
            ->when($search, function ($query) use ($search) {
                $query->where('content', 'like', '%'.$search.'%');
            })
            ->withCount('comments', 'likes')
            ->orderBy('created_at', 'desc')
            ->paginate($postPerPage);
        //add a column to check whether the auth user is following the user who created the post or not, if following, return true, else return false
        $posts->map(function ($post) {
            $id = $post->user_id;
            //check whether the auth()->id, and post user_id is in user_followers table or not
            if (auth()->user()->following->contains($id)) {
                $post->is_following = true;
            } else {
                $post->is_following = false;
            }

            return $post;
        });

        return $this->success(data: $posts, message: 'Posts retrieved successfully');
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
    public function store(PostRequest $request)
    {
        $userId = Auth::user();
        // Set the timezone to Malaysia
        date_default_timezone_set('Asia/Kuala_Lumpur');

        // Get the pictures from the request
        $pictures = $request->picture;

        // if no picture
        if (! $pictures) {
            // Save the post
            $post = new Post;
            $post->user_id = $userId->user_id;
            $post->content = $request->input('content');
            $post->picture = null;
            $post->created_at = now();
            $post->save();

            // Send notifications to tagged users
            $this->sendTaggedUserNotifications($request->input('content'));

            return response()->json(['message' => 'Posts created successfully']);
        } else {
            foreach ($pictures as $picture) {
                // Get the image format (png, jpg, jpeg)
                $imageFormat = explode('/', mime_content_type($picture['url']))[1];

                // Decode the base64-encoded image URL
                $decodedImage = base64_decode(str_replace('data:image/'.$imageFormat.';base64,', '', $picture['url']));

                // Generate a unique file name with the correct extension
                $fileName = uniqid().'.'.$imageFormat;

                // Store the image in the storage/app/public/images/posts directory
                Storage::disk('public')->put('images/posts/'.$fileName, $decodedImage);

                // Save the post
                $post = new Post;
                $post->user_id = $userId->user_id;
                $post->content = $request->input('content');
                $post->picture = 'images/posts/'.$fileName; // Set the picture_path
                $post->created_at = now();
                $post->save();
            }

            // Send notifications to tagged users
            $this->sendTaggedUserNotifications($request->input('content'));

            return response()->json(['message' => 'Posts created successfully']);
        }
    }

    private function sendTaggedUserNotifications($content)
    {
        // Send notification to the users who are tagged in the post
        // The tagged users are in the content of the post that start with @ and end with space,
        // for example, @user1 @user2, so the tagged users are user1 and user2, it is the user's username
        $taggedUsers = explode(' ', $content);
        $taggedUsers = array_filter($taggedUsers, function ($taggedUser) {
            return strpos($taggedUser, '@') === 0;
        });

        foreach ($taggedUsers as $taggedUser) {
            $taggedUser = str_replace('@', '', $taggedUser);
            $taggedUser = User::where('username', $taggedUser)->first();

            if ($taggedUser) {
                $notification = new Notification();
                $notification->receiver_id = $taggedUser->user_id;
                $notification->sender_id = Auth::id();
                $notification->information = Auth::user()->username.' tagged you in a post';
                $notification->status = 'Unread';
                $notification->created_at = now();

                $notification->save();
            }
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
        $post = Post::findOrFail($id);

        // Update the post content
        $post->content = $request->input('content');

        // Update the post picture if a new picture was provided
        if ($request->picture !== null) {
            $picture = $request->picture;
            $imageFormat = explode('/', mime_content_type($picture))[1];
            $decodedImage = base64_decode(str_replace('data:image/'.$imageFormat.';base64,', '', $picture));
            $fileName = uniqid().'.'.$imageFormat;
            Storage::disk('public')->put('images/posts/'.$fileName, $decodedImage);
            $post->picture = 'images/posts/'.$fileName;
        } else {
            $post->picture = null;
        }

        $post->save();

        return response()->json(['message' => 'Post updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::findorFail($id);

        if (! $post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }

    //like post
    public function likePost(Request $request, $postId)
    {
        $userId = Auth::id();
        date_default_timezone_set('Asia/Kuala_Lumpur');

        // User has not liked the post yet, so like it
        $like = new Like;
        $like->user_id = $userId;
        $like->post_id = $postId;
        $like->created_at = now();
        $like->save();

        // create notification for like if the user is not the post owner
        $post = Post::find($postId);

        if ($post->user_id != $userId) {
            $notification = new Notification();
            $notification->receiver_id = $post->user_id;
            $notification->sender_id = $userId;
            $notification->information = Auth::user()->username.' liked your post';
            $notification->status = 'Unread';
            $notification->created_at = now();

            $notification->save();
        }

        return response()->json(['message' => 'Post liked successfully']);

    }

    public function unlikePost(Request $request, $postId)
    {
        $userId = Auth::id();

        // User has liked the post, so unlike it
        Like::where('user_id', $userId)->where('post_id', $postId)->delete();

        return response()->json(['message' => 'Post unliked successfully']);
    }

    public function getAuthPosts()
    {
        $userId = Auth::id();

        $posts = Post::with('user', 'comments', 'likes')
            ->where('user_id', $userId)
            ->withCount('comments', 'likes')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success(data: $posts, message: 'Posts retrieved successfully');
    }
}
