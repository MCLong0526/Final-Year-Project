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
        // Set the timezone to Malaysia
        date_default_timezone_set('Asia/Kuala_Lumpur');
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Check if an order already exists for the same item_id and user_id
        $existingOrder = DB::table('item_user')
            ->where('item_id', $request->item_id)
            ->where('buyer_id', $userId)
            ->where('status', 'pending')
            ->first();

        if ($existingOrder) {
            return response()->json(['message' => 'Order already placed for this item.'], 409);
        }

        $orderDateTime = new DateTime();

        // Save the order to the pivot table item_user
        Item::findOrFail($request->item_id)->users()->attach($userId, [
            'status' => $request->status,
            'quantity' => $request->quantity,
            'place_to_meet' => $request->place_to_meet,
            'order_dateTime' => $orderDateTime,
            'remark_buyer' => $request->remark_buyer,
        ]);

        // set the notification to the seller
        $sellerId = Item::findOrFail($request->item_id)->user_id;
        $itemName = Item::findOrFail($request->item_id)->name;
        $information = "I have placed an order for your item '$itemName'. Please check it out!";
        DB::table('notifications')->insert([
            'sender_id' => $userId,
            'receiver_id' => $sellerId,
            'information' => $information,
            'status' => 'Unread',
            'created_at' => $orderDateTime,
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

    public function getPendingSellOrders()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Get all items belongs to the user
        $itemIds = Item::where('user_id', $userId)->pluck('item_id');

        // Get all orders for the authenticated user
        $allOrders = DB::table('item_user')
            ->whereIn('item_id', $itemIds)
            ->where('status', 'Pending')
            ->orderBy('order_dateTime', 'asc')
            ->get();

        // Get the users, items, and pictures separately based on the user_id, item_id, and item_pictures
        foreach ($allOrders as $order) {
            $order->user = User::find($order->buyer_id);
            $order->item = Item::find($order->item_id);

            // Get the pictures for the item
            $order->item->pictures = ItemPicture::where('item_id', $order->item_id)->get();
        }

        return $this->success(data: $allOrders, message: 'All selling orders retrieved successfully');
    }

    public function getConfirmedSellOrders()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Get all items belongs to the user
        $itemIds = Item::where('user_id', $userId)->pluck('item_id');
        // Get all orders for the authenticated user
        $allOrders = DB::table('item_user')
            ->whereIn('item_id', $itemIds)
            ->where('status', 'Approved')
            ->orWhere('status', 'Rejected')
            //  order by meet_dateTime in ascending order but ignore the null values
            ->orderByRaw('meet_dateTime IS NULL, meet_dateTime ASC')
            ->get();

        // Get the users, items, and pictures separately based on the user_id, item_id, and item_pictures
        foreach ($allOrders as $order) {
            $order->user = User::find($order->buyer_id);
            $order->item = Item::find($order->item_id);

            // Get the pictures for the item
            $order->item->pictures = ItemPicture::where('item_id', $order->item_id)->get();
        }

        // filter the orders by using the request "search" parameter, and the request search is to search the buyer's username and email, the search can also be used to filter the item's name
        if (request()->filled('search')) {
            $allOrders = $allOrders->filter(function ($order) {
                return stripos($order->user->username, request('search')) !== false
                    || stripos($order->user->email, request('search')) !== false
                    || stripos($order->item->name, request('search')) !== false;
            });
        }
        if (request()->filled('status')) {
            // direct compare the status with the request status
            $allOrders = $allOrders->filter(function ($order) {
                return $order->status === request('status');
            });
        }

        return $this->success(data: $allOrders, message: 'All selling orders retrieved successfully');
    }

    public function confirmedOrder(Request $request, string $id)
    {
        // Set the timezone to Malaysia
        date_default_timezone_set('Asia/Kuala_Lumpur');

        if ($request->meet_dateTime === null) {
            DB::table('item_user')->where('id', $id)->update([
                'status' => 'Rejected',
                'meet_dateTime' => $request->meet_dateTime,
                'remark_seller' => $request->remark_seller,
            ]);
        } else {
            // check the meet_dateTime is not null
            $request->validate([
                'meet_dateTime' => 'required',
            ]);
            DB::table('item_user')->where('id', $id)->update([
                'status' => 'Approved',
                'meet_dateTime' => $request->meet_dateTime,
                'remark_seller' => $request->remark_seller,
            ]);
        }

        // set the notification to the buyer
        $order = DB::table('item_user')->where('id', $id)->first();
        $buyerId = $order->buyer_id;
        $itemName = Item::findOrFail($order->item_id)->name;
        $information = "Your order for the item '$itemName' has been $order->status by the seller.";
        DB::table('notifications')->insert([
            'sender_id' => Auth::id(),
            'receiver_id' => $buyerId,
            'information' => $information,
            'status' => 'Unread',
            'created_at' => now(),

        ]);

        return $this->success('Order approved successfully');
    }

    public function countPendingOrders()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // get all the item_id that belongs to the user
        $itemIds = Item::where('user_id', $userId)->pluck('item_id');

        //compare the item_id with the item_id in the item_user table and get the pending orders
        $pendingOrders = DB::table('item_user')
            ->whereIn('item_id', $itemIds)
            ->where('status', 'pending')
            ->count();

        return $this->success(data: $pendingOrders, message: 'Pending orders counted successfully');
    }

    // get purchases pending orders
    public function getPurchasesOrders()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Get all Approved and Rejected orders for the authenticated user
        $buyOrders = DB::table('item_user')
            ->where('buyer_id', $userId)
            ->get();

        // Get the users, items, and pictures separately based on the user_id, item_id, and item_pictures
        foreach ($buyOrders as $order) {
            $order->user = User::find($order->buyer_id);
            $order->item = Item::find($order->item_id);

            // Get the pictures for the item
            $order->item->pictures = ItemPicture::where('item_id', $order->item_id)->get();
            $order->item->user = User::find($order->item->user_id);
        }

        return $this->success(data: $buyOrders, message: 'Purchases orders retrieved successfully');
    }

    public function cancelPendingOrder(string $id)
    {
        // Set the timezone to Malaysia
        date_default_timezone_set('Asia/Kuala_Lumpur');

        // Get the order details
        $order = DB::table('item_user')->where('id', $id)->first();

        // Check if the order is pending
        if ($order->status === 'Pending') {
            // Update the order status to Rejected
            DB::table('item_user')->where('id', $id)->update([
                'status' => 'Cancelled',
                'remark_buyer' => 'Order cancelled by buyer',
            ]);

            // set the notification to the seller
            $sellerId = Item::findOrFail($order->item_id)->user_id;
            $itemName = Item::findOrFail($order->item_id)->name;
            $information = "The order for your item '$itemName' has been cancelled by the buyer.";
            DB::table('notifications')->insert([
                'sender_id' => Auth::id(),
                'receiver_id' => $sellerId,
                'information' => $information,
                'status' => 'Unread',
                'created_at' => now(),
            ]);

            return $this->success('Order cancelled successfully');
        } else {
            return response()->json(['message' => 'Order cannot be cancelled.'], 409);
        }
    }

    // count the number of pending, approved, rejected, and cancelled of the authenticated user orders
    public function countAuthPurchases()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Get all orders for the authenticated user
        $allOrders = DB::table('item_user')
            ->where('buyer_id', $userId)
            ->get();

        // Count the number of orders based on the status
        $pendingOrders = $allOrders->where('status', 'Pending')->count();
        $approvedOrders = $allOrders->where('status', 'Approved')->count();
        $rejectedOrders = $allOrders->where('status', 'Rejected')->count();
        $cancelledOrders = $allOrders->where('status', 'Cancelled')->count();

        return $this->success(data: [
            'noPendingOrders' => $pendingOrders,
            'noApprovedOrders' => $approvedOrders,
            'noRejectedOrders' => $rejectedOrders,
            'noCancelledOrders' => $cancelledOrders,
        ], message: 'Orders counted successfully');
    }

    public function getOrderPersons(string $id)
    {
        // Get the order details, and get only approved orders
        $order = DB::table('item_user')->where('item_id', $id)->where('status', 'Approved')->get();

        if ($order->isEmpty()) {
            return $this->success(data: [], message: 'No buyers details found');
        } else {
            //get all the buyer_id from the order
            $buyerIds = $order->pluck('buyer_id');

            // get the buyer details
            $buyers = User::whereIn('user_id', $buyerIds)->get();

            //get only the username and user_id
            $buyers = $buyers->map(function ($buyer) {
                return [
                    'username' => $buyer->username,
                    'user_id' => $buyer->user_id,
                ];
            });

            return $this->success(data: $buyers, message: 'Buyers details retrieved successfully');
        }
    }
}
