<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get all the user data
        $users = User::get();

        return $this->success(data: $users, message: 'Users retrieved successfully');
        //return response()->json(['message' => 'Users retrieved successfully', 'data' => $users]);
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
        $user = User::create($request->validated());

        // Find the role with role_id = 2
        $role = Role::where('role_id', 2)->firstOrFail();

        // Attach the role to the user
        $user->roles()->attach($role);

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
    public function destroy(string $user_id)
    {

        $user = User::where('user_id', $user_id)->firstOrFail();
        $user->delete();

        return $this->success(message: 'User deleted successfully');

    }
}
