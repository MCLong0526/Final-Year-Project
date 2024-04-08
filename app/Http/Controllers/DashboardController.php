<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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

    public function getNumberOfLikes()
    {
        $user = Auth::user();
        $posts = Post::where('user_id', $user->user_id)->get();

        $totalLikes = 0;
        $newLikesCount = 0;
        $newLikesPercentage = 0.00;

        // calculate the total likes from the posts and percentage change in this month
        foreach ($posts as $post) {
            $totalLikes += $post->likes->count();
            // calculate the new likes from the posts in this month
            $newLikes = $post->likes->where('created_at', '>=', now()->startOfMonth());
            $newLikesCount += $newLikes->count();
        }

        //get only two decimal points, make sure will have negative also
        if ($totalLikes > 0) {
            $newLikesPercentage = number_format(($newLikesCount / $totalLikes) * 100, 2);
        }

        return response()->json(['total_likes' => $totalLikes, 'new_likes_percentage' => $newLikesPercentage]);
    }

    public function getNumberOfPosts()
    {
        $user = Auth::user();
        $posts = Post::where('user_id', $user->user_id)->get();
        $count = $posts->count();

        //calculate the percentage of new added posts in the this month
        $newPosts = $posts->where('created_at', '>=', now()->startOfMonth());
        $newPostsCount = $newPosts->count();
        //get only two decimal points, make it can more than 100%
        $newPostsPercentage = number_format(($newPostsCount / $count) * 100, 2);

        return response()->json(['number_of_posts' => $count, 'new_posts_percentage' => $newPostsPercentage]);
    }

    public function getNumberOfItems()
    {
        $user = Auth::user();
        $items = Item::where('user_id', $user->user_id)->get();
        $count = $items->count();

        //calculate the percentage of new added items in the this month for the current authenticated user
        $newItems = $items->where('created_at', '>=', now()->startOfMonth());

        $newItemsCount = $newItems->count();
        //get only two decimal points, make sure will have negative also
        $newItemsPercentage = number_format(($newItemsCount / $count) * 100, 2);

        return response()->json(['number_of_items' => $count, 'new_items_percentage' => $newItemsPercentage]);
    }

    public function getNumberOfServices()
    {
        $user = Auth::user();
        $services = Service::where('user_id', $user->user_id)->get();
        $count = $services->count();

        //calculate the percentage of new added services in the this month
        $newServices = $services->where('created_at', '>=', now()->startOfMonth());
        $newServicesCount = $newServices->count();
        //get only two decimal points, make sure will have negative also
        $newServicesPercentage = number_format(($newServicesCount / $count) * 100, 2);

        return response()->json(['number_of_services' => $count, 'new_services_percentage' => $newServicesPercentage]);
    }

    public function getAuthEarned()
    {
        // Get the current authenticated user
        $user = Auth::user();

        // Get all the services where the user_id is equal to the current authenticated user's id
        $services = Service::where('user_id', $user->user_id)->get();

        // Use DB to get all the service_user entries where the service's user_id is equal to the current authenticated user's id
        foreach ($services as $service) {
            $service_user = DB::table('service_user')->where('service_id', $service->service_id)->get();
            // store all the $service_user to $all_auth_service_user, else no need to store it
            if ($service_user) {
                $all_auth_service_user[] = $service_user;
            }

        }
        $total_earned = 0;
        $number_of_approved = 0;
        $number_of_pending = 0;
        $number_of_rejected = 0;
        // calculate all the approximated_price from the $all_auth_service_user, but the status must be equal "Approved" and save it to $all_auth_service_user_approved
        foreach ($all_auth_service_user as $service_user) {
            foreach ($service_user as $service_user) {
                if ($service_user->status == 'Approved') {
                    $total_earned += $service_user->approximated_price;
                    $number_of_approved++;
                } elseif ($service_user->status == 'Pending') {
                    $number_of_pending++;
                } elseif ($service_user->status == 'Rejected') {
                    $number_of_rejected++;
                }
            }
        }

        return $this->success([
            'total_earned' => $total_earned,
            'number_of_approved' => $number_of_approved,
            'number_of_pending' => $number_of_pending,
            'number_of_rejected' => $number_of_rejected,
        ]);
    }

    // public function getSchedules()
    // {
    //     $user = Auth::user();
    //     $services = Service::where('user_id', $user->user_id)->get();
    //     $items = Item::where('user_id', $user->user_id)->get();

    //     $all_schedules = [];

    //     // use DB to get all the service_user where the service_id is equal to the $service->service_id
    //     foreach ($services as $service) {
    //         $service_user = DB::table('service_user')->where('service_id', $service->service_id)->get();
    //         // get all the $service_user where the status is equal to "Approved"
    //         foreach ($service_user as $service_user) {
    //             if ($service_user->status == 'Approved') {
    //                 $all_schedules[] = $service_user;
    //             }
    //         }
    //     }

    //     // use DB to get all the item_user where the item_id is equal to the $item->item_id
    //     foreach ($items as $item) {
    //         $item_user = DB::table('item_user')->where('item_id', $item->item_id)->get();
    //         // get all the $item_user where the status is equal to "Approved"
    //         foreach ($item_user as $item_user) {
    //             if ($item_user->status == 'Approved') {
    //                 $all_schedules[] = $item_user;
    //             }
    //         }
    //     }

    //     return $this->success($all_schedules);

    // }
}
