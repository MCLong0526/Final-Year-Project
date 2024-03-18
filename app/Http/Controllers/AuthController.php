<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    use HasApiTokens; // Use HasApiTokens trait

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

    public function authenticate(LoginRequest $loginRequest)
    {
        $credentials = $loginRequest->validated();

        if (Auth::attempt($credentials)) {
            $loginRequest->session()->regenerate();

            $user = User::with('roles')->find(Auth::id());
            $token = hash('sha256', Str::random(80));

            //create a new token
            $user->api_token = $token;
            $user->save();

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        }

        return response()->json(['error' => 'Incorrect username or password. Please try again.'], 401);
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/login')->with('success', 'Logout successful');
    }

    public function getCurrentLoggedUser()
    {
        $user = User::with('roles')->find(Auth::id());

        return $this->success(
            data: $user,
            message: 'User found'
        );
    }

    public function getUserByToken(Request $request)
    {
        $user = User::with('roles')->where('api_token', $request->bearerToken())->first();

        if (! $user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['user' => $user]);
    }
}
