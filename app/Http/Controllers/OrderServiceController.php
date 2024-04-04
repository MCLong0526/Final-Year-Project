<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderServiceRequest;
use App\Models\Service;
use DateTime;
use Illuminate\Http\Request;
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

        // set the notification to the seller
        $providerId = Service::findOrFail($request->service_id)->user_id;
        $serviceName = Service::findOrFail($request->service_id)->name;
        $information = "I have placed an order for your service '$serviceName'. Please check it out!";
        DB::table('notifications')->insert([
            'sender_id' => $userId,
            'receiver_id' => $providerId,
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
}
