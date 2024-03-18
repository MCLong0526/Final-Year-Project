<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfileRequest;
use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'password' => 'required|string|min:8',
        ]);

        $user = User::findOrFail(Auth::id());
        $user->update(['password' => bcrypt($data['password'])]);

        return $this->success(data: $user, message: 'User password updated successfully');
    }

    // Update the user profile picture
    public function updateAvatar(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        // Check if the request contains a file named 'avatar'
        if ($request->hasFile('avatar')) {

            $avatar = $request->file('avatar');

            // Store the uploaded file in the 'public/images/avatars' directory
            $avatarPath = $avatar->store('images/avatars', 'public');

            // Update the user's avatar path in the database
            $user->update(['avatar' => $avatarPath]);

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
}
