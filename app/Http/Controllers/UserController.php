<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfileRequest;
use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
                    ->orWhere('email', 'like', '%'.request('search').'%');
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
        $defaultAvatarPath = 'images/avatars/avatar-1.png';

        $data = $request->validated();

        // Set default avatar path
        $data['avatar'] = $defaultAvatarPath;

        $user = User::create($data);

        // attach role_id = 2 to the user
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
}
