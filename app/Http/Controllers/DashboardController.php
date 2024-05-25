<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
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
        if ($count > 0) {
            $newPostsPercentage = number_format(($newPostsCount / $count) * 100, 2);
        } else {
            $newPostsPercentage = 0;
        }

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
        if ($count > 0) {
            $newItemsPercentage = number_format(($newItemsCount / $count) * 100, 2);
        } else {
            $newItemsPercentage = 0;
        }

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
        if ($count > 0) {
            $newServicesPercentage = number_format(($newServicesCount / $count) * 100, 2);
        } else {
            $newServicesPercentage = 0;
        }

        return response()->json(['number_of_services' => $count, 'new_services_percentage' => $newServicesPercentage]);
    }

    public function getAuthEarned()
    {
        $total_earned_service = 0;
        $number_of_approved_service = 0;
        $number_of_pending_service = 0;
        $number_of_rejected_service = 0;
        $number_of_cancelled_service = 0;

        $total_earned_item = 0;
        $number_of_approved_item = 0;
        $number_of_pending_item = 0;
        $number_of_rejected_item = 0;
        $number_of_cancelled_item = 0;

        // Get the current authenticated user
        $user = Auth::user();

        // Get all the services where the user_id is equal to the current authenticated user's id
        $services = Service::where('user_id', $user->user_id)->get();
        $items = Item::where('user_id', $user->user_id)->get();

        $all_auth_service_user = [];
        $all_auth_item_user = [];

        // Use DB to get all the service_user entries where the service's user_id is equal to the current authenticated user's id
        foreach ($services as $service) {
            $service_user = DB::table('service_user')->where('service_id', $service->service_id)->get();
            // store all the $service_user to $all_auth_service_user, else no need to store it
            if ($service_user) {
                $all_auth_service_user[] = $service_user;
            }
        }
        foreach ($items as $item) {
            $item_user = DB::table('item_user')->where('item_id', $item->item_id)->get();
            // store all the $item_user to $all_auth_service_user, else no need to store it
            if ($item_user) {
                $all_auth_item_user[] = $item_user;
            }
        }

        // calculate all the approximated_price from the $all_auth_service_user, but the status must be equal "Approved" and save it to $all_auth_service_user_approved
        foreach ($all_auth_service_user as $service_user) {
            foreach ($service_user as $service_user) {
                if ($service_user->status == 'Approved') {
                    $total_earned_service += $service_user->approximated_price;
                    $number_of_approved_service++;
                } elseif ($service_user->status == 'Pending') {
                    $number_of_pending_service++;
                } elseif ($service_user->status == 'Rejected') {
                    $number_of_rejected_service++;
                } elseif ($service_user->status == 'Cancelled') {
                    $number_of_cancelled_service++;
                }
            }
        }

        // calculate all the approximated_price from the $all_auth_item_user, but the status must be equal "Approved" and save it to $all_auth_item_user_approved
        foreach ($all_auth_item_user as $item_user) {
            foreach ($item_user as $item_user) {
                if ($item_user->status == 'Approved') {
                    $total_earned_item += $item_user->approximated_price;
                    $number_of_approved_item++;
                } elseif ($item_user->status == 'Pending') {
                    $number_of_pending_item++;
                } elseif ($item_user->status == 'Rejected') {
                    $number_of_rejected_item++;
                } elseif ($item_user->status == 'Cancelled') {
                    $number_of_cancelled_item++;
                }
            }
        }

        return $this->success([
            'total_earned_service' => $total_earned_service,
            'number_of_approved_service' => $number_of_approved_service,
            'number_of_pending_service' => $number_of_pending_service,
            'number_of_rejected_service' => $number_of_rejected_service,
            'number_of_cancelled_service' => $number_of_cancelled_service,
            'total_earned_item' => $total_earned_item,
            'number_of_approved_item' => $number_of_approved_item,
            'number_of_pending_item' => $number_of_pending_item,
            'number_of_rejected_item' => $number_of_rejected_item,
            'number_of_cancelled_item' => $number_of_cancelled_item,
        ]);
    }

    public function getWeeklyEarned(Request $request)
    {
        // Get the current authenticated user
        $user = Auth::user();

        // Get type from request or default to 'both'
        $type = $request->get('type', 'both');

        $total_earned = 0;
        $number_of_approved = 0;

        //filter it by this week only according to the service_dateTime, it is a string so we can use the Carbon method
        $week_start = Carbon::now('Asia/Kuala_Lumpur')->startOfWeek();
        $week_end = Carbon::now('Asia/Kuala_Lumpur')->endOfWeek();

        // Calculate the total earned for each day of the week
        $earned_by_day = [
            'Monday' => 0,
            'Tuesday' => 0,
            'Wednesday' => 0,
            'Thursday' => 0,
            'Friday' => 0,
            'Saturday' => 0,
            'Sunday' => 0,
        ];

        if ($type === 'services' || $type === 'both') {
            // Fetch and process service data
            $services = Service::where('user_id', $user->user_id)->get();
            foreach ($services as $service) {
                $service_user = DB::table('service_user')
                    ->where('service_id', $service->service_id)
                    ->where('status', 'Approved')
                    ->get();
                foreach ($service_user as $service_user) {
                    [$date, $timeRange] = explode(' ', $service_user->service_dateTime);
                    [$startTime, $endTime] = explode('-', $timeRange);

                    $service_dateTime = new Carbon("{$date} {$startTime}");
                    if ($service_dateTime->between($week_start, $week_end)) {
                        $day_of_week = $service_dateTime->isoFormat('dddd');
                        $earned_by_day[$day_of_week] += $service_user->approximated_price;
                        $number_of_approved++;
                    }
                }
            }
        }

        if ($type === 'items' || $type === 'both') {
            // Fetch and process item data in a similar way
            $items = Item::where('user_id', $user->user_id)->get();
            foreach ($items as $item) {
                $item_user = DB::table('item_user')
                    ->where('item_id', $item->item_id)
                    ->where('status', 'Approved')
                    ->get();
                foreach ($item_user as $item_user) {
                    $meet_dateTime = new Carbon($item_user->meet_dateTime);
                    if ($meet_dateTime->between($week_start, $week_end)) {
                        $day_of_week = $meet_dateTime->isoFormat('dddd');
                        $earned_by_day[$day_of_week] += $item_user->approximated_price;
                        $number_of_approved++;
                    }
                }
            }
        }

        // Format the total earned for each day
        $formatted_earned_by_day = [];
        foreach ($earned_by_day as $day => $earned) {
            $formatted_earned_by_day[$day] = number_format($earned, 2);
        }

        // total earned in a week
        foreach ($formatted_earned_by_day as $day => $earned) {
            $total_earned += $earned;
        }

        return $this->success([
            'total_earned' => $formatted_earned_by_day,
            'number_of_approved' => $number_of_approved,
            'total_earned_week' => number_format($total_earned, 2),
        ]);
    }

    public function getMonthlyEarned(Request $request)
    {
        // Get the current authenticated user
        $user = Auth::user();

        // Get type from request or default to 'both'
        $type = $request->get('type', 'both');

        $total_earned = 0;
        $number_of_approved = 0;

        //filter it by this year only according to the service_dateTime, it is a string so we can use the Carbon method
        $year_start = now()->startOfYear();
        $year_end = now()->endOfYear();

        // Calculate the total earned for each month of the year
        $earned_by_month = [
            'January' => 0,
            'February' => 0,
            'March' => 0,
            'April' => 0,
            'May' => 0,
            'June' => 0,
            'July' => 0,
            'August' => 0,
            'September' => 0,
            'October' => 0,
            'November' => 0,
            'December' => 0,
        ];

        if ($type === 'services' || $type === 'both') {
            // Fetch and process service data
            $services = Service::where('user_id', $user->user_id)->get();
            foreach ($services as $service) {
                $service_user = DB::table('service_user')
                    ->where('service_id', $service->service_id)
                    ->where('status', 'Approved')
                    ->get();
                foreach ($service_user as $service_user) {
                    $service_dateTime = new Carbon($service_user->service_dateTime);
                    if ($service_dateTime->between($year_start, $year_end)) {
                        $month_of_year = $service_dateTime->isoFormat('MMMM');
                        $earned_by_month[$month_of_year] += $service_user->approximated_price;
                        $number_of_approved++;
                    }
                }
            }
        }

        if ($type === 'items' || $type === 'both') {
            // Fetch and process item data in a similar way
            $items = Item::where('user_id', $user->user_id)->get();
            foreach ($items as $item) {
                $item_user = DB::table('item_user')
                    ->where('item_id', $item->item_id)
                    ->where('status', 'Approved')
                    ->get();
                foreach ($item_user as $item_user) {
                    $meet_dateTime = new Carbon($item_user->meet_dateTime);
                    if ($meet_dateTime->between($year_start, $year_end)) {
                        $month_of_year = $meet_dateTime->isoFormat('MMMM');
                        $earned_by_month[$month_of_year] += $item_user->approximated_price;
                        $number_of_approved++;
                    }
                }
            }
        }

        // Format the total earned for each month
        $formatted_earned_by_month = [];
        foreach ($earned_by_month as $month => $earned) {
            $formatted_earned_by_month[$month] = number_format($earned, 2);
        }

        // total earned in a year
        foreach ($formatted_earned_by_month as $month => $earned) {
            $total_earned += $earned;
        }

        return $this->success([
            'total_earned' => $formatted_earned_by_month,
            'number_of_approved' => $number_of_approved,
            'total_earned_year' => number_format($total_earned, 2),
        ]);
    }
}
