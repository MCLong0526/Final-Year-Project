<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Models\Item;
use App\Models\ItemPicture;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
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
    public function store(CreateOrderRequest $request)
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Check if an order already exists for the same item_id and user_id
        $existingOrder = DB::table('item_user')
            ->where('item_id', $request->item_id)
            ->where('user_id', $userId)
            ->first();

        if ($existingOrder) {
            return response()->json(['message' => 'Order already placed for this item.'], 409);
        }

        // Set the timezone to the user's timezone
        date_default_timezone_set('Asia/Kuala_Lumpur');
        $orderDateTime = new DateTime();

        // Save the order to the pivot table item_user
        Item::findOrFail($request->item_id)->users()->attach($userId, [
            'status' => $request->status,
            'quantity' => $request->quantity,
            'place_to_meet' => $request->place_to_meet,
            'order_dateTime' => $orderDateTime,
            'remark_buyer' => $request->remark_buyer,
        ]);

        return $this->success('Order placed successfully');

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

    public function getPendingOrders()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Get all items belongs to the user
        $itemIds = Item::where('user_id', $userId)->pluck('item_id');

        // Get all pending orders for the authenticated user
        $pendingOrders = DB::table('item_user')
            ->whereIn('item_id', $itemIds)
            ->where('status', 'pending')
            ->get();

        // Get the users, items, and pictures separately based on the user_id, item_id, and item_pictures
        foreach ($pendingOrders as $order) {
            $order->user = User::find($order->user_id);
            $order->item = Item::find($order->item_id);

            // Get the pictures for the item
            $order->item->pictures = ItemPicture::where('item_id', $order->item_id)->get();
        }

        return $this->success(data: $pendingOrders, message: 'Pending orders retrieved successfully');
    }

    public function approveOrder(Request $request, string $id)
    {
        // update the item_user table with the status 'Approved', and the $id is the item_user.id
        // update also the meet_dateTime, remark_seller
        DB::table('item_user')->where('id', $id)->update([
            'status' => 'Approved',
            'meet_dateTime' => $request->meet_dateTime,
            'remark_seller' => $request->remark_seller,
        ]);

        return $this->success('Order approved successfully');
    }
}
