<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Post;
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

            return response()->json(['message' => 'Posts created successfully']);
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
}
