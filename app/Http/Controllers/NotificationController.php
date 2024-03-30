<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
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

    public function getAuthNotifications()
    {
        // get all the notification of authenticated user, get with the buyer and seller details
        $notifications = Notification::where('receiver_id', Auth::id())
            ->with('sender', 'receiver')
            ->get();

        // if the user is

        return $this->success($notifications, 'Notifications retrieved successfully.', 200);
    }

    public function markAsRead(string $notificationId)
    {
        // Get the notification object based on the notification ID
        $notification = Notification::find($notificationId);

        // Update the notification status to Read
        $notification->status = 'Read';
        $notification->save();

        return $this->success('Notification marked as read successfully.', 200);
    }

    public function markAllRead(string $userId)
    {
        // Get all notifications belongs to the user, seller_id and buyer_id is the authenticated user's ID
        $notifications = Notification::where('receiver_id', $userId)
            ->get();

        // Update the notification status to Read
        foreach ($notifications as $notification) {
            $notification->status = 'Read';
            $notification->save();
        }

        return $this->success('All notifications marked as read successfully.', 200);
    }

    public function countUnreadNotifications()
    {
        // Get all notifications belongs to the user, seller_id and buyer_id is the authenticated user's ID, for sale and buy orders
        $notifications = Notification::where('receiver_id', Auth::id())
            ->where('status', 'Unread')
            ->get();

        return $this->success($notifications, 'Unread notifications count retrieved successfully.', 200);
    }
}
