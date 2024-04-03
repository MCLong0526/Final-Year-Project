<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
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
        //
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

    public function getNumberOfUsers()
    {
        $users = User::all();
        $count = $users->count();

        //calculate the percentage of new added users in the last week
        $newUsers = User::where('created_at', '>=', now()->subWeek())->get();
        $newUsersCount = $newUsers->count();
        //get only two decimal points
        $newUsersPercentage = number_format(($newUsersCount / $count) * 100, 2);

        return response()->json(['number_of_users' => $count, 'new_users' => $newUsersCount, 'new_users_percentage' => $newUsersPercentage]);
    }

    public function getNumberOfPosts()
    {
        $posts = Post::all();
        $count = $posts->count();

        //calculate the percentage of new added posts in the last week
        $newPosts = Post::where('created_at', '>=', now()->subWeek())->get();
        $newPostsCount = $newPosts->count();
        //get only two decimal points, make it can more than 100%
        $newPostsPercentage = number_format(($newPostsCount / $count) * 100, 2);

        return response()->json(['number_of_posts' => $count, 'new_posts' => $newPostsCount, 'new_posts_percentage' => $newPostsPercentage]);
    }

    public function getNumberOfItems()
    {
        $items = Item::all();
        $count = $items->count();

        //calculate the percentage of new added items in the last week
        $newItems = Item::where('created_at', '>=', now()->subWeek())->get();
        $newItemsCount = $newItems->count();
        //get only two decimal points, make sure will have negative also
        $newItemsPercentage = number_format(($newItemsCount / $count) * 100, 2);

        return response()->json(['number_of_items' => $count, 'new_items' => $newItemsCount, 'new_items_percentage' => $newItemsPercentage]);
    }
}
