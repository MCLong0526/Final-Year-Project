<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Post;
use App\Models\Service;
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

    public function getNumberOfServices()
    {
        $services = Service::all();
        $count = $services->count();

        //calculate the percentage of new added services in the last week
        $newServices = Service::where('created_at', '>=', now()->subWeek())->get();
        $newServicesCount = $newServices->count();
        //get only two decimal points, make sure will have negative also
        $newServicesPercentage = number_format(($newServicesCount / $count) * 100, 2);

        return response()->json(['number_of_services' => $count, 'new_services' => $newServicesCount, 'new_services_percentage' => $newServicesPercentage]);
    }

    public function getStatusUsers()
    {
        $users = User::all();
        $count = $users->count();

        // calculate the number of active users and inactive users, and the percentage of active users
        $activeUsers = User::where('status', 'active')->get();
        $activeUsersCount = $activeUsers->count();
        $inactiveUsersCount = $count - $activeUsersCount;
        $activeUsersPercentage = number_format(($activeUsersCount / $count) * 100, 2);

        return response()->json(['active_users' => $activeUsersCount, 'inactive_users' => $inactiveUsersCount, 'active_users_percentage' => $activeUsersPercentage]);
    }

    public function getAllPercentageTypeItems()
    {
        $items = Item::all();
        $count = $items->count();

        // get all the type of items and calculate the percentage of each of the type of items,
        $typeItems = Item::select('type')->distinct()->get();
        $typeItemsCount = [];
        $typeItemsPercentage = [];
        foreach ($typeItems as $typeItem) {
            $typeItemsCount[$typeItem->type] = Item::where('type', $typeItem->type)->count();
            $typeItemsPercentage[$typeItem->type] = number_format(($typeItemsCount[$typeItem->type] / $count) * 100, 2);
        }

        // sort the array by the percentage and get only the top 5
        arsort($typeItemsPercentage);
        $typeItemsPercentage = array_slice($typeItemsPercentage, 0, 5);

        // change it to an array with name and percentage
        $typeItemsPercentage = array_map(function ($key, $value) {
            return ['name' => $key, 'percentage' => $value];
        }, array_keys($typeItemsPercentage), $typeItemsPercentage);

        return response()->json(['type_items_percentage' => $typeItemsPercentage]);
    }
}
