<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfileRequest;
use App\Http\Requests\UserRequest;
use App\Mail\NewAccountPasswordMail;
use App\Models\Item;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userPerPage = request()->input('per_page', 10);

        $users = User::with('roles')
            ->when(request()->filled('search'), function ($query) {
                $query->where('username', 'like', '%'.request('search').'%')
                    ->orWhere('email', 'like', '%'.request('search').'%')
                    ->orWhere('phone_number', 'like', '%'.request('search').'%');
            })
            //filter the users by role id
            ->when(request()->filled('role'), function ($query) {
                $query->whereHas('roles', function ($query) {
                    $query->whereIn('role_user.role_id', explode(',', request('role')));
                });
            })
            //filter the users by status, use explode to convert the comma separated string to an array
            ->when(request()->filled('status'), function ($query) {
                $query->whereIn('status', explode(',', request('status')));
            })

            ->paginate($userPerPage);

        return $this->success(data: $users, message: 'Users retrieved successfully');

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
    public function store(UserRequest $request)
    {
        // Set default avatar path
        $defaultAvatarPath = 'images/avatars/avatar-20.jpg';

        // Validate request data
        $validatedData = $request->validated();

        // Hash the password
        $validatedData['password'] = bcrypt($validatedData['password']);

        // Set default avatar path if not provided
        $validatedData['avatar'] = $request->filled('avatar') ? $validatedData['avatar'] : $defaultAvatarPath;

        // Create user
        $user = User::create($validatedData);

        // Send the new password to the user email
        try {
            Mail::to($user->email)->send(new NewAccountPasswordMail($request->password));
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            return response()->json(['error' => 'Failed to send email'], 500);
        }

        // Attach role_id = 2 to the user
        $user->roles()->attach(Role::where('role_id', 2)->first());

        return $this->success(data: $user, message: 'User created successfully');
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

    public function editPassword(Request $request)
    {
        $data = $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

        $user = User::findOrFail(Auth::id());
        $user->update(['password' => bcrypt($data['password'])]);

        return $this->success(data: $user, message: 'User password updated successfully');
    }

    public function editAvatar(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        if ($request->has('avatar')) {

            //if request avatar is started with 'image/avatars/...' then direct save the image
            if (strpos($request->input('avatar'), 'images/avatars/') === 0) {
                $user->update(['avatar' => $request->input('avatar')]);

                return $this->success(data: $user, message: 'User profile picture updated successfully');
            }

            $avatarData = $request->input('avatar');

            // Remove the data URL prefix and decode the base64 data
            $avatarData = substr($avatarData, strpos($avatarData, ',') + 1);
            $avatarData = base64_decode($avatarData);

            // Generate a unique file name
            $avatarFileName = 'avatar_'.$user->id.'_'.time().'.png';

            // Store the avatar image
            Storage::disk('public')->put('images/avatars/'.$avatarFileName, $avatarData);

            // Update the user's avatar path in the database
            $user->update(['avatar' => 'images/avatars/'.$avatarFileName]);

            return $this->success(data: $user, message: 'User profile picture updated successfully');
        }

        return $this->error(message: 'Avatar file not found in the request');
    }

    // user can edit their details
    public function editProfile(UserProfileRequest $request, string $id)
    {
        $data = $request->validated();

        $user = User::findOrFail($id);
        $user->update($data);

        return $this->success(data: $user, message: 'User profile updated successfully');
    }

    /**
     * Update the specified resource in storage.
     */

    // Admin can update user details and roles
    public function update(UserRequest $request, string $id)
    {
        $data = $request->validated();

        $user = User::findOrFail($id);
        $user->update($data);

        // Check if 'roles' data is present in the request and if it's an array
        if (isset($data['roles']) || is_array($data['roles'])) {

            $user->roles()->sync($data['roles']);
        }

        return $this->success(data: $user, message: 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $user_id)
    {

        $user = User::where('user_id', $user_id)->firstOrFail();
        $user->delete();

        return $this->success(message: 'User deleted successfully');

    }

    //get current logged user password
    public function checkPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
        ]);

        $user = auth()->user();

        return response()->json([
            'password_match' => Hash::check($request->current_password, $user->password),
        ]);
    }

    public function followUser(string $id)
    {
        //if the $id and auth()->id() are the same, return an error message
        if ($id === auth()->id()) {
            return response()->json(['message' => 'You cannot follow yourself'], 400);
        }

        $user = User::where('user_id', $id)->firstOrFail();

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check whether the auth()->id() is already following the $id, make sure there is no duplicate following
        if (auth()->user()->following->contains($id)) {
            return response()->json(['message' => 'User already followed'], 400);
        }

        // Attach the user to the authenticated user's followings using the create method
        $user->following()->attach($id, ['user_id' => auth()->id(), 'following_id' => $id]);

        return response()->json(['message' => 'User followed successfully', 'user' => $user]);
    }

    public function unfollowUser(string $id)
    {
        $user = User::where('user_id', $id)->firstOrFail();

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check whether the auth()->id() is already following the $id
        if (auth()->user()->following->contains($id)) {
            // delete the user from the authenticated user's followings
            auth()->user()->following()->detach($id);

            return response()->json(['message' => 'User unfollowed successfully', 'user' => $user]);
        }

        return response()->json(['message' => 'User is not followed yet', 'user' => $user]);
    }

    public function getFollowing()
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $following = $user->following;

        return $this->success(data: $following, message: 'Following retrieved successfully');
    }

    public function getUserDetails(string $id)
    {
        $user = User::where('user_id', $id)->firstOrFail();

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        } else {
            // check whether the authenticated user is following the user
            $user->is_followed = auth()->user()->following->contains($id);

            //calculate the number of items the user has, make sure the user_id of the item is the same as the $id
            $items = Item::where('user_id', $user->user_id)->get();
            $count = $items->count();
            $user->items_count = $count;

            //calculate the number of services the user has, make sure the user_id of the service is the same as the $id
            $services = Service::where('user_id', $user->user_id)->get();
            $count = $services->count();
            $user->services_count = $count;

            //calculate the number of followers the user has, using the following table because don not have a followers table, just check the following table where the following_id is the same as the $id
            $followers = User::whereHas('following', function ($query) use ($id) {
                $query->where('following_id', $id);
            })->get();
            $count = $followers->count();
            $user->followers_count = $count;

            // Calculate the rating of the user for items
            $rating1 = DB::table('item_user')
                ->whereIn('item_id', $items->pluck('item_id')->toArray()) // Use whereIn instead of where
                ->whereNotNull('rating')
                ->avg('rating');

            // Combine the rating to the service_user table, make sure the service_user's service_id is the same as the service's service_id, get only the service_user that has been rated
            $rating2 = DB::table('service_user')
                ->whereIn('service_id', $services->pluck('service_id')->toArray()) // Use whereIn instead of where
                ->whereNotNull('rating')
                ->avg('rating');

            // Calculate the average rating of the user
            $rating = null;

            if ($rating1 !== null && $rating2 !== null) {
                $rating = ($rating1 + $rating2) / 2;
            } elseif ($rating1 !== null) {
                $rating = $rating1;
            } elseif ($rating2 !== null) {
                $rating = $rating2;
            }

            // Format the rating with 2 decimal places
            $user->ratings = ($rating !== null) ? number_format($rating, 2) : null;

        }

        return $this->success(data: $user, message: 'User details retrieved successfully');
    }

    // public function saveToken(Request $request)
    // {
    //     $user = Auth::user();
    //     if ($user) {
    //         $user->device_token = $request->token;
    //         $user->save();

    //         return response()->json(['success' => true], 200);
    //     }

    //     return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
    // }
}
