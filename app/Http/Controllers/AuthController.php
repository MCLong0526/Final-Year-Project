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

            $user = User::with('roles')->find(Auth::id());
            $token = hash('sha256', Str::random(80));

            // Save the token in the user's record (if you need to store it in the database)
            //check if the user has a token
            if ($user->api_token) {
                //update the token
                $user->api_token = $token;
                $user->save();
            } else {
                //create a new token
                $user->api_token = $token;
                $user->save();
            }

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        }

        return response()->json(['error' => 'Incorrect username or password. Please try again.'], 401);
    }

    public function logout()
    {
        Auth::guard('web')->logout();

        return $this->success(message: 'Logout successful');
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
        $user = User::where('api_token', $request->bearerToken())->first();

        if (! $user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['user' => $user]);
    }

    public function saveToken(Request $request)
    {
        $user = Auth::user();
        $user->api_token = $request->token;
        $user->save();

        return response()->json(['message' => 'Token saved successfully']);
    }
}
