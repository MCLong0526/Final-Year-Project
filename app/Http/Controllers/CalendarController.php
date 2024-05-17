<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemPicture;
use App\Models\Service;
use App\Models\ServicePicture;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
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

    public function upComingMeetupSeller()
    {
        //get from service_user, and item_user, get all the upcoming meetups, get for the item_user first
        //then get for the service_user
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Get all items belongs to the user
        $itemIds = Item::where('user_id', $userId)->pluck('item_id');
        // Get all orders for the authenticated user
        $allItemOrders = DB::table('item_user')
            ->whereIn('item_id', $itemIds)
            ->where('status', 'Approved')
            ->get();

        // Get the users, items, and pictures separately based on the user_id, item_id, and item_pictures
        foreach ($allItemOrders as $order) {
            $order->user = User::find($order->buyer_id);
            $order->item = Item::find($order->item_id);

            // Get the pictures for the item
            $order->item->pictures = ItemPicture::where('item_id', $order->item_id)->get();
        }

        // now get for the service_user
        $serviceIds = Service::where('user_id', $userId)->pluck('service_id');
        // Get all orders for the authenticated user
        $allServiceOrders = DB::table('service_user')
            ->whereIn('service_id', $serviceIds)
            ->where('status', 'Approved')

            ->get();

        // Get the users, items, and pictures separately based on the user_id, item_id, and item_pictures
        foreach ($allServiceOrders as $order) {
            $order->user = User::find($order->customer_id);
            $order->service = Service::find($order->service_id);

            // Get the pictures for the item
            $order->service->pictures = ServicePicture::where('service_id', $order->service_id)->get();
        }

        return $this->success(data: [
            'item_orders' => $allItemOrders,
            'service_orders' => $allServiceOrders,
        ]);
    }

    public function upComingMeetupBuyer()
    {
        //get from service_user, and item_user, get all the upcoming meetups, get for the item_user first
        //then get for the service_user
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Get all orders for the authenticated user
        $allItemOrders = DB::table('item_user')
            ->where('buyer_id', $userId)
            ->where('status', 'Approved')
            ->get();

        // Get the users, items, and pictures separately based on the user_id, item_id, and item_pictures
        foreach ($allItemOrders as $order) {
            $order->user = User::find($order->buyer_id);
            $order->item = Item::find($order->item_id);

            // Get the pictures for the item
            $order->item->pictures = ItemPicture::where('item_id', $order->item_id)->get();
        }

        // Get all orders for the authenticated user
        $allServiceOrders = DB::table('service_user')
            ->where('customer_id', $userId)
            ->where('status', 'Approved')
            ->get();

        // Get the users, items, and pictures separately based on the user_id, item_id, and item_pictures
        foreach ($allServiceOrders as $order) {
            $order->user = User::find($order->customer_id);
            $order->service = Service::find($order->service_id);

            // Get the pictures for the item
            $order->service->pictures = ServicePicture::where('service_id', $order->service_id)->get();
        }

        return $this->success(data: [
            'item_orders' => $allItemOrders,
            'service_orders' => $allServiceOrders,
        ]);
    }
}
