<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderServiceRequest;
use App\Models\Service;
use App\Models\ServicePicture;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderServiceController extends Controller
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
    public function store(OrderServiceRequest $request)
    {
        // Set the timezone to Malaysia
        date_default_timezone_set('Asia/Kuala_Lumpur');
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Check if an order already exists for the same service_id and user_id
        $existingOrder = DB::table('service_user')
            ->where('service_id', $request->service_id)
            ->where('customer_id', $userId)
            ->where('status', 'pending')
            ->first();

        if ($existingOrder) {
            return response()->json(['message' => 'Order already placed for this item.'], 400);
        }

        $orderDateTime = new DateTime();

        // Save the order to the pivot table item_user
        Service::findOrFail($request->service_id)->users()->attach($userId, [
            'status' => $request->status,
            'place_to_service' => $request->place_to_service,
            'order_dateTime' => $orderDateTime,
            'approximated_price' => $request->approximated_price,
            'remark_customer' => $request->remark_customer,
        ]);

        // Retrieve the ID of the newly inserted row in the service_user pivot table
        $orderId = DB::table('service_user')
            ->where('service_id', $request->service_id)
            ->where('customer_id', $userId)
            ->where('order_dateTime', $orderDateTime)
            ->value('id');

        // set the notification to the seller
        $providerId = Service::findOrFail($request->service_id)->user_id;
        $serviceName = Service::findOrFail($request->service_id)->name;
        $information = "I have placed an order for your service '$serviceName'. Please check it out!";
        DB::table('notifications')->insert([
            'sender_id' => $userId,
            'receiver_id' => $providerId,
            'information' => $information,
            'type' => 'OrderService',
            'related_id' => $orderId,
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

        // Get all services belongs to the user
        $serviceIds = Service::where('user_id', $userId)->pluck('service_id');

        // Get all orders for the authenticated user
        $allOrders = DB::table('service_user')
            ->whereIn('service_id', $serviceIds)
            ->where('status', 'Pending')
            ->orderBy('order_dateTime', 'asc')
            ->get();

        // Get the users, services, and pictures separately based on the user_id, service_id, and service_pictures
        foreach ($allOrders as $order) {
            $order->user = User::find($order->customer_id);
            $order->service = Service::find($order->service_id);

            // Get the pictures for the service
            $order->service->pictures = ServicePicture::where('service_id', $order->service_id)->get();
        }

        return $this->success(data: $allOrders, message: 'All selling orders retrieved successfully');
    }

    public function getConfirmedSellOrders()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Get all items belongs to the user
        $serviceIds = Service::where('user_id', $userId)->pluck('service_id');
        // Get all orders for the authenticated user
        $allOrders = DB::table('service_user')
            ->whereIn('service_id', $serviceIds)
            ->where('status', 'Approved')
            ->orWhere('status', 'Rejected')

            ->get();

        // Get the users, items, and pictures separately based on the user_id, item_id, and item_pictures
        foreach ($allOrders as $order) {
            $order->user = User::find($order->customer_id);
            $order->service = Service::find($order->service_id);

            // Get the pictures for the item
            $order->service->pictures = ServicePicture::where('service_id', $order->service_id)->get();
        }

        // filter the orders by using the request "search" parameter, and the request search is to search the buyer's username and email, the search can also be used to filter the item's name
        if (request()->filled('search')) {
            $allOrders = $allOrders->filter(function ($order) {
                return stripos($order->user->username, request('search')) !== false
                    || stripos($order->user->email, request('search')) !== false
                    || stripos($order->service->name, request('search')) !== false;
            });
        }
        if (request()->filled('status')) {
            // direct compare the status with the request status
            $allOrders = $allOrders->filter(function ($order) {
                return $order->status === request('status');
            });
        }
        if (request()->filled('order_date')) {
            // direct compare the order_date with the request order_date, format of order_date is 22/09/2021 - 23/09/2021, format of order_dateTime is '2024-05-12 12:54:00'
            $orderDates = explode(' - ', request('order_date'));
            $startDate = Carbon::parse($orderDates[0])->startOfDay();
            $endDate = Carbon::parse($orderDates[1])->endOfDay();
            $allOrders = $allOrders->filter(function ($order) use ($startDate, $endDate) {
                return Carbon::parse($order->order_dateTime)->between($startDate, $endDate);
            });
        }
        if (request()->filled('service_date')) {
            //remove the orders that service_dateTime is null
            $allOrders = $allOrders->filter(function ($order) {
                return ! is_null($order->service_dateTime);
            });
            // direct compare the service_date with the request service_date, format of service_date is 22/09/2021 - 23/09/2021, the service_dateTime is the date and time
            $serviceDates = explode(' - ', request('service_date'));
            $startDate = Carbon::parse($serviceDates[0])->startOfDay();
            $endDate = Carbon::parse($serviceDates[1])->endOfDay();
            $allOrders = $allOrders->filter(function ($order) use ($startDate, $endDate) {
                return Carbon::parse($order->service_dateTime)->between($startDate, $endDate);
            });
        }

        // Sort the filtered results to ensure upcoming orders are first, now the service_dateTime is in string format of '2024-05-21 07:00-09:00', so just sort the date only
        $allOrders = $allOrders->sortBy(function ($order) {
            $now = now();

            if (is_null($order->service_dateTime)) {
                return PHP_INT_MAX; // Ensure null values are at the end
            }

            // Extract the date part from the string
            $datePart = explode(' ', $order->service_dateTime)[0];
            $serviceDateTime = Carbon::parse($datePart);

            if ($serviceDateTime->isFuture()) {
                return $serviceDateTime->timestamp; // Future dates come first
            } elseif ($serviceDateTime->isToday()) {
                return 0; // Today's dates should be prioritized
            } else {
                return PHP_INT_MAX + $serviceDateTime->timestamp; // Past dates should come after future and today's dates
            }
        })->values();

        return $this->success(data: $allOrders, message: 'All services orders retrieved successfully');
    }

    public function confirmedOrder(Request $request, string $id)
    {
        // Set the timezone to Malaysia
        date_default_timezone_set('Asia/Kuala_Lumpur');

        if ($request->service_dateTime === null) {
            // return error message if the remark_provider is empty
            $request->validate([
                'remark_provider' => 'required',
            ]);
            DB::table('service_user')->where('id', $id)->update([
                'status' => 'Rejected',
                'service_dateTime' => $request->service_dateTime,
                'remark_provider' => $request->remark_provider,
            ]);
        } else {
            DB::table('service_user')->where('id', $id)->update([
                'status' => 'Approved',
                'service_dateTime' => $request->service_dateTime,
                'remark_provider' => $request->remark_provider,
            ]);
        }

        // set the notification to the customer
        $order = DB::table('service_user')->where('id', $id)->first();
        $customerId = $order->customer_id;
        $serviceName = Service::findOrFail($order->service_id)->name;
        $information = "Your order for the service '$serviceName' has been $order->status by the service provider.";
        DB::table('notifications')->insert([
            'sender_id' => Auth::id(),
            'receiver_id' => $customerId,
            'information' => $information,
            'type' => 'ConfirmedOrderService',
            'related_id' => $id,
            'status' => 'Unread',
            'created_at' => now(),

        ]);

        return $this->success('Order approved successfully');
    }

    // get purchases pending orders
    public function getPurchasesOrders()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Get all Approved and Rejected orders for the authenticated user
        $buyOrders = DB::table('service_user')
            ->where('customer_id', $userId)
            ->get();

        // Get the users, services, and pictures separately based on the user_id, service_id, and service_pictures
        foreach ($buyOrders as $order) {
            $order->user = User::find($order->customer_id);
            $order->service = Service::find($order->service_id);

            // Get the pictures for the service
            $order->service->pictures = ServicePicture::where('service_id', $order->service_id)->get();
            $order->service->user = User::find($order->service->user_id);
        }

        return $this->success(data: $buyOrders, message: 'Purchases orders retrieved successfully');
    }

    public function cancelPendingOrder(string $id)
    {
        // Set the timezone to Malaysia
        date_default_timezone_set('Asia/Kuala_Lumpur');

        // Get the order details
        $order = DB::table('service_user')->where('id', $id)->first();

        // Check if the order is pending
        if ($order->status === 'Pending') {
            // Update the order status to Rejected
            DB::table('service_user')->where('id', $id)->update([
                'status' => 'Cancelled',
                'remark_customer' => 'Order cancelled by customer',
            ]);

            // set the notification to the seller
            $sellerId = Service::findOrFail($order->service_id)->user_id;
            $serviceName = Service::findOrFail($order->service_id)->name;
            $information = "The order for your item '$serviceName' has been cancelled by the customer.";
            DB::table('notifications')->insert([
                'sender_id' => Auth::id(),
                'receiver_id' => $sellerId,
                'information' => $information,
                'type' => 'CancelOrderService',
                'related_id' => $id,
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
        $allOrders = DB::table('service_user')
            ->where('customer_id', $userId)
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

    public function countPendingOrders()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // get all the service_id that belongs to the user
        $serviceIds = Service::where('user_id', $userId)->pluck('service_id');

        //compare the service_id with the service_id in the service_user table and get the pending orders
        $pendingOrders = DB::table('service_user')
            ->whereIn('service_id', $serviceIds)
            ->where('status', 'pending')
            ->count();

        return $this->success(data: $pendingOrders, message: 'Pending orders counted successfully');
    }

    public function rateProvider(Request $request)
    {

        // Find the order service
        $order = DB::table('service_user')->where('id', $request->order_id)->first();

        // Check if the order exists
        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Check if the order status is Approved
        if ($order->status !== 'Approved') {
            return response()->json(['message' => 'Order not approved'], 409);
        }

        // update the rating for the seller
        DB::table('service_user')->where('id', $request->order_id)->update([
            'rating' => $request->rating,
        ]);

        // Optionally, you can return a response
        return response()->json(['message' => 'Rating submitted'], 200);
    }
}
